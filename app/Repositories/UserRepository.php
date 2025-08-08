<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\UserPermission;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findExistingUser(array $data): ?User
    {
        return User::where('client_group_id', $data['client_group_id'])
            ->where('client_network_id', $data['client_network_id'])
            ->where('domain_name', $data['domain'])
            ->where('cirms_userid', $data['userid'])
            ->where('username', $data['username'])
            ->where('software_id', 2)
            ->where('is_deleted', 0)
            ->first();
    }

    public function isUsernameUsed(string $username, int $clientNetworkId): bool
    {
        return User::where('username', $username)
            ->where('client_network_id', $clientNetworkId)
            ->where('is_deleted', 0)
            ->exists();
    }

    public function createUser(array $userData): User
    {
        $userId = User::insertGetId($userData);
        return User::find($userId);
    }

    public function assignAdminPermission(int $userId): void
    {
        UserPermission::insert([
            'user_id' => $userId,
            'code' => 100,
        ]);
    }

    public function generateTempToken(User $user, string $tokenName = 'bypass_login_token', int $minutes = 5): string
    {
        /* Create the token */
        $token = $user->createToken($tokenName, ['*'])->plainTextToken;
        /* Extract the token ID from the token string  */
        [$tokenId, $tokenHash] = explode('|', $token);
        /* Set expiration */
        PersonalAccessToken::where('id', $tokenId)->update([
            'expires_at' => Carbon::now()->addMinutes($minutes)->toDateTimeString()
        ]);

        return $token;
    }

    public function findUserByToken(string $token): ?User
    {
        /* Validate token */
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken || ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at))) {
            optional($accessToken)->delete();
            return null;
        }

        return $accessToken->tokenable;
    }

    public function checkAdminPermission($userId)
    {
        return DB::table('user_permission')
            ->where('user_id', $userId)
            ->where('code', 100)
            ->exists();
    }
}
