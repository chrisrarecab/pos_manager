<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\User;

interface UserRepositoryInterface
{
    public function findExistingUser(array $data): ?User;
    public function isUsernameUsed(string $username, int $clientNetworkId): bool;
    public function createUser(array $userData): User;
    public function assignAdminPermission(int $userId): void;
    public function generateTempToken(User $user, string $tokenName = 'bypass_login_token', int $minutes = 5): string;
    public function findUserByToken(string $token): ?User;
    public function checkAdminPermission($userId);
}
