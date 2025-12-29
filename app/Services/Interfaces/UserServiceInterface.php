<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function findById($id): ?User;

    public function update(string $userId, array $data): User;

    public function updateIfExists(string $userId, array $data): ?User;
}
