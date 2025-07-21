<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserPermission;
use Carbon\Carbon;

use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\ClientBaseApiController;
use App\Http\Controllers\CirmsApiController;
use App\DataTransferObjects\RepositoryResponse;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Hash;
use Session;

class UserRepository
{
    public function bypassRegisterCirmsRepo(Request $request): RepositoryResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
            'userid' => 'required|string|max:100',
            'username' => 'required|string|alpha_dash|max:50',
            'fullname' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'admin' => 'sometimes|boolean'
        ]);

        try {
            /* check if domain exist in client base as network name */
            $api = new ClientBaseApiController();
            $response = $api->verifyClientDomain($request->domain);
            $clientBaseValues = $response['data']['values'];
            $clientGroupId = $clientBaseValues['clientGroupId'];
            $clientNetworkId = $clientBaseValues['clientNetworkId'];
            $domain = $clientBaseValues['domain'];

            /* validate user via cirms api */
            try {
                $cirmsApi = new CirmsApiController();
                $cirmsResponse = $cirmsApi->userAuthentication($request);
            } catch (ConnectionException $e) {
                return RepositoryResponse::failure('Connection failed.',  'Domain does not exist or is unreachable.' );
            } catch (\Exception $e) {
                return RepositoryResponse::failure('Something went wrong.', $e->getMessage());
            }   

            $existingUser = User::where('client_group_id', $clientGroupId)
                ->where('client_network_id', $clientNetworkId)
                ->where('domain_name', $request->domain)
                ->where('cirms_userid', $request->userid)
                ->where('username', $request->username)
                ->where('software_id', 2)
                ->where('is_deleted', 0)
                ->first();

            if ($existingUser) {
                if (!Hash::check($request->password, $existingUser->password)) {
                    return RepositoryResponse::failure('Invalid credentials.', [
                        'password' => 'Incorrect password.',
                    ]);
                }

                $token = $this->generateTempToken($existingUser);

                return RepositoryResponse::success("User verified.", [
                    'token' => $token,
                    'url' => '/login/cirms'
                ]);
            }

            $existingUsername = User::where('username', $request->username)
                ->where('client_network_id', $clientNetworkId)
                ->where('is_deleted', 0)
                ->exists();

            if ($existingUsername) {
                return RepositoryResponse::failure("Request failed","Username already used in this client network.");
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
                'client_network_id' => $clientNetworkId,
                'password' => Hash::make($request->password),
                'software_id' => 2,
                'domain_name' => $request->domain,
                'cirms_userid' => $request->userid,

            ]);

            if (isset($request->admin) || $request->username == 'admin') {
                UserPermission::insert([
                    'user_id' => $userId,
                    'code' => 100,
                ]);
            }

            $user = User::find($userId);
            $token = $this->generateTempToken($user);

            return RepositoryResponse::success("User created successfully", [
                'token' => $token,
                'url' => '/login/cirms'
            ]);

        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return RepositoryResponse::failure("Registration failed", $e->getMessage());
        }
    }

    public function bypassLoginRepo(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return RepositoryResponse::failure('Request failed.', 'Token is required.', 400);
            }
            
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return RepositoryResponse::failure('Request failed.', 'Invalid token.', 401);
            }
            
            if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
                $accessToken->delete();
                return RepositoryResponse::failure('Request failed.', 'Token is expired.', 401);
            }

            Auth::login($accessToken->tokenable);

            $userId = $accessToken->tokenable_id;
            $user = User::find($userId);
            $request->session()->regenerate();
                session()->put('userId', $user->id);
                session()->put('clientGroupId', $user->client_group_id);
                session()->put('fullName', $user->full_name);
                session()->put('sourceProjectId', $user->software_id);
                session()->save();

            return RepositoryResponse::success('Login successful.', [
                'redirect' => url('/dashboard'),
            ]);

        } catch (SignatureInvalidException $e) {
            return RepositoryResponse::failure('Invalid token signature.',  $e->getMessage(), 401);
        } catch (\Exception $e) {
            return RepositoryResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }
    
    private function generateTempToken(User $user, string $tokenName = 'bypass_login_token', int $minutes = 5): string
    {
        // Create the token
        $token = $user->createToken($tokenName, ['*'])->plainTextToken;

        // Extract the token ID from the token string 
        [$tokenId, $tokenHash] = explode('|', $token);

        // Set expiration
        PersonalAccessToken::where('id', $tokenId)->update([
            'expires_at' => Carbon::now()->addMinutes($minutes)->toDateTimeString()
        ]);

        return $token;
    }

}