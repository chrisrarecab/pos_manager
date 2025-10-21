<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\DataTransferObjects\ServiceResponse;
use App\Http\Controllers\ClientBaseApiController;
use App\Http\Controllers\CirmsApiController;
use App\Http\Requests\User\StoreUserRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}
    
    public function login(array $data) : ServiceResponse
    {
        try {
            // DB::enableQueryLog();
            $isSuccessful = Auth::attempt([
                'username' => $data['username'],
                'password' => $data['password'],
                'software_id' => $data['softwareId'],
                'domain_name' => $data['domainName'] ?? '',
            ]);
            
            if (!$isSuccessful) {
                return ServiceResponse::failure('Login failed.', 'Incorrect username or password', 400);
            }
        }
        catch (Exception $e) {
            Log::channel('systemErrorLog')->error('Login:' .$e->getMessage());
            return ServiceResponse::failure('Login failed.', 'Authentication error', 500);
        }

        return ServiceResponse::success("Login successful.");
    }
    public function bypassRegisterCirms(array $validated): ServiceResponse
    {
        try {
            /* Checks if domain is valid */
            $api = app(ClientBaseApiController::class);
            $response = $api->verifyClientDomain($validated['domain']);
            $clientBaseValues = $response['data']['values'];

            $clientGroupId = $clientBaseValues['clientGroupId'];
            $clientNetworkId = $clientBaseValues['clientNetworkId'];
            $domain = $clientBaseValues['domain'];

            /* Checks if user exist*/
            $existingUser = $this->userRepo->findExistingUser([
                'client_group_id' => $clientGroupId,
                'client_network_id' => $clientNetworkId,
                'domain' => $validated['domain'],
                'userid' => $validated['userid'],
                'username' => $validated['username']
            ]);

            if ($existingUser) {
                if (!Hash::check($validated['password'], $existingUser->password)) {
                    return ServiceResponse::failure('Invalid credentials.', [
                        'password' => 'Incorrect password.',
                    ]);
                }

                $token = $this->userRepo->generateTempToken($existingUser);

                return ServiceResponse::success("User verified.", [
                    'token' => $token,
                    'url' => '/login/cirms'
                ]);
            }

            /*  If user doesn't exist, check if username exist */
            if ($this->userRepo->isUsernameUsed($validated['username'], $clientNetworkId)) {
                return ServiceResponse::failure("Request failed", "Username already used in this client network.");
            }

            $user = $this->userRepo->createUser([
                'username' => $validated['username'],
                'full_name' => $validated['fullname'],
                'status' => 1,
                'created_date' => now(),
                'last_modified_date' => now(),
                'created_by' => 1,
                'last_modified_by' => 1,
                'client_group_id' => $clientGroupId,
                'client_network_id' => $clientNetworkId,
                'password' => Hash::make($validated['password']),
                'software_id' => 2,
                'domain_name' => $validated['domain'],
                'cirms_userid' => $validated['userid'],
            ]);

            if (isset($validated['admin']) || $validated['username'] === 'admin') {
                $this->userRepo->assignAdminPermission($user->id);
            }

            $token = $this->userRepo->generateTempToken($user);

            return ServiceResponse::success("User created successfully", [
                'token' => $token,
                'url' => '/login/cirms'
            ]);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return ServiceResponse::failure("Registration failed", $e->getMessage());
        }
    }

    public function bypassLoginCirms(Request $request): ServiceResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return ServiceResponse::failure('Request failed.', 'Token is required.', 400);
            }

            $user = $this->userRepo->findUserByToken($token);

            if (!$user) {
                return ServiceResponse::failure('Request failed.', 'Invalid or expired token.', 401);
            }

            $isAdmin = $this->userRepo->checkAdminPermission($user->id);

            auth()->login($user);
            $request->session()->regenerate();
            session()->put('userId', $user->id);
            session()->put('isAdmin', $isAdmin);
            session()->put('fullName', $user->full_name);
            session()->put('clientGroupId', $user->client_group_id);
            session()->put('clientNetworkId', $user->client_network_id);
            session()->put('domain', $user->domain_name);
            session()->put('software_id', $user->software_id);
            session()->save();
            return ServiceResponse::success('Login successful.', [
                'redirect' => url('/dashboard'),
            ]);
        } catch (\Exception $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }
}
