<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\ClientBaseApiController;
use App\Http\Controllers\CirmsApiController;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


use Hash;
use Session;

class UserController extends Controller
{   
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getSession(Request $request)
    {
        $data['session'] = session()->all();
        return response()->json([
            'values' => $data,
        ], 200);
    }

    public function checkAuth()
    {
        $userId = session()->get('userId');
        return $userId > 0;
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete(); 
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->flush();
        return redirect('/login');
    }

    public function login(Request $request)
    {
        switch ($request->project) {
            case 1:
                $credentials = ['username' => $request->username, 'password' => $request->password, 'status' => 1, 'is_deleted' => 0, 'software_id' => 1];
                break;
            
            case 2:
                $credentials = ['username' => $request->username, 'password' => $request->password, 'status' => 1, 'is_deleted' => 0, 'software_id' => 2];
                break;

            default:
                break;
        }

        try {
            $isSuccessful = Auth::attempt($credentials);
            if (!$isSuccessful) {
                return response()->json(['error' => 'Incorrect username or password'], 400);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Authentication error (500)'], 500);
        }

        if ($request->project == 2) {
            $api = new ClientBaseApiController();
            $api->verifyClientDomain($request->domain);
            $cirmsApi = new CirmsApiController();
            $cirmsApi->userAuthentication($request);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        session()->regenerate();
        session()->put('userId', $user->id);
        session()->put('clientGroupId', $user->client_group_id);
        session()->put('fullName', $user->full_name);
        session()->put('sourceProjectId', $user->software_id);
        session()->save();
        return response()->json([
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
        ], 200);
    }

    public function registerBySecretKey(Request $request)
    {
        $request->validate([
            'secretkey' => 'required',
            'username' => 'required',
            'fullname' => 'required|max:100',
            'password' => 'required|min:6',
        ]);
    
        $api = new ClientBaseApiController();
        $response = $api->validateSecretKey($request->secretkey);
        $clientGroupId = $response['data']['values']['clientGroupId'];
        $usernameExistByGroup = User::select('username')->where('username', $request->username)->where('is_deleted', 0)->where('client_group_id', $clientGroupId)->get();
        
        if ($usernameExistByGroup->isNotEmpty()) {
            throw ValidationException::withMessages(['message' => "Username already used."]);
        }
        $userId = User::insertGetId([
            'username' => $request->username,
            'full_name' => $request->fullname,
            'status' => 1,
            'created_date' => now(),
            'last_modified_date' => now(),
            'created_by' => 1,
            'last_modified_by' => 1,
            'client_group_id' => $clientGroupId,
            'password' => Hash::make($request->password),
            'software_id' => 1,
        ]);

        if (isset($request->admin)) {
            UserPermission::insert([
                'user_id' => $userId,
                'code' => 100,
            ]);
        }
        $data['secretKey'] = $request->secretkey;
        $response = $api->activateSecretKey($request->secretkey, $userId);
        return response()->json([
            "errors" => [],
            "message" => "Successfully created.",
            "isSuccessful" => true,
            "userId" => $userId,
            "status" => 200,
        ], 200);
    }

    public function bypassRegisterCirms(Request $request)
    {
        $response = $this->userRepo->bypassRegisterCirmsRepo($request);
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ], $response->success ? 200 : 500);
    }

    public function bypassLoginCirms(Request $request)
    {
        $response = $this->userRepo->bypassLoginRepo($request);
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }
}
