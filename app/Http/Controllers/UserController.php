<?php

namespace App\Http\Controllers;

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
use Laravel\Sanctum\PersonalAccessToken;
use Inertia\Inertia;

use App\Models\User;
use App\Models\UserPermission;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\ClientBaseApiController;
use App\Http\Controllers\CirmsApiController;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;


use Hash;
use Session;

class UserController extends Controller
{   

    public function __construct(
        protected UserService $userService,
        protected UserRepositoryInterface $userRepo
    ) {}

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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');

    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $response = $this->userService->login($validated);

        if (!$response->success) {
            throw ValidationException::withMessages([
                'password' => $response->error,
            ]);
        }
        
        $user = $request->user();
        $request->session()->regenerate(); 
     
        session()->put([
            'userId'         => $user->id,
            'fullName'       => $user->full_name,
            'username'       => $user->username,
            'clientGroupId'  => $user->client_group_id,
            'clientNetworkId'=> $user->client_network_id,
            'domain'         => $user->domain_name,
            'softwareId'     => $user->software_id,
        ]);
        return redirect()->route('dashboard')
                         ->with('success', $response->message);

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

    public function bypassRegisterCirms(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $response = $this->userService->bypassRegisterCirms($validated);

        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ], $response->statusCode);
    }

    public function bypassLoginCirms(Request $request)
    {
        $response = $this->userService->bypassLoginCirms($request);

        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

}
