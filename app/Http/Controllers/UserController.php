<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use App\Http\Controllers\ClientBaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\Middleware\StartSession;
use Hash;
use Session;

class UserController extends Controller
{   
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
        session()->flush();
        return redirect('/login');
    }

    public function login(Request $request)
    {
        $credentials = ['username' => $request->username, 'password' => $request->password, 'status' => 1];

        try {
            $isSuccessful = Auth::attempt($credentials);
            if (!$isSuccessful) {
                return response()->json(['error' => 'Incorrect username or password'], 400);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Authentication error (500)'], 500);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        session()->regenerate();
        session()->put('userId', $user->id);
        session()->put('clientGroupId', $user->client_group_id);
        session()->put('fullName', $user->full_name);
        session()->save();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
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

        $endpoint = $api->getUrl("validate_secret");
        $params = "?secretKey=".$request->secretkey;
        $response = Http::withHeaders(["Authorization" => $api->getAuthorization()])->get($endpoint.$params);

        if (! isset($response['isSuccessful'])) {
            throw ValidationException::withMessages(['message' => "Error: Unable to reach server."]);
        }

        if ($response['isSuccessful'] == false) {
            throw ValidationException::withMessages(['message' => $response['error']]);
        }
        
        $clientGroupId = $response['data']['values']['clientGroupId'];
        
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
        ]);

        if (isset($request->admin)) {
            UserPermission::insert([
                'user_id' => $userId,
                'code' => 100,
            ]);
        }
        
        $data['secretKey'] = $request->secretkey;
        $endpoint = $api->getUrl("activate_secret");
        $response = Http::withHeaders(["Authorization" => $api->getAuthorization()])->post($endpoint, [
            "secretKey" => $request->secretkey, 
            "posManagerUserId" => $userId,
        ]);

        return response()->json([
            "errors" => [],
            "message" => "OK",
            "isSuccessful" => true,
            "userId" => $userId,
            "status" => 200,
        ], 200);
    }
}
