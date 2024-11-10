<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\ClientBaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Middleware\StartSession;
use Hash;

class UserController extends Controller
{   
    public function checkUserSession(Request $request)
    {
        $userId = null;
        $isSuccessful = false;
        $statusResponse = 404;

        if (Auth::check()) {
            $userId = Auth::user()->id;
            $isSuccessful = true;
            $statusResponse = 200;
        }
        
        return response()->json([
            'isSuccessful' => $isSuccessful,
            'userId' => $userId,
        ], $statusResponse);
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
        $request->session()->regenerate();

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
            'username' => 'required|unique:users',
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
