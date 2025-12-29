<?php

namespace App\Services;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;


class UserService implements UserServiceInterface
{
    public function findById($id): ?User
    {
        return User::find($id);
    }

    public function update(string $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->update($data);

        return $user->fresh();
    }

    public function updateIfExists(string $userId, array $data): ?User
    {
        $user = User::find($userId);

        if ($user) {
            $user->update($data);
            return $user->fresh();
        }

        return null;
    }
}
