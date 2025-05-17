<?php

namespace App\Services;

use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService
{
    function __construct()
    {
    }

    public function processRegister(string $name, string $email, string $password): array
    {
        $role = Role::query()->where('name', 'user')->first();

        DB::beginTransaction();

        $user = User::create([
            "id" => Str::uuid(),
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $role->id,
        ]);

        $token = $this->createToken($user);

        DB::commit();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function processLogin(string $email): array
    {
        $user = User::query()->where('email', $email)->first();

        $token = $this->createToken($user);

        $this->removeRedundantTokens($email);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function processLogout(User $user): void
    {
        $user->tokens()->delete();
    }

    private function createToken(User $user): string
    {
        $token = $user->createToken("Token of user: {$user->name}");

        return $token->plainTextToken;
    }

    private function removeRedundantTokens(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        $tokens = PersonalAccessToken::query()->where('tokenable_id', $user->id)->oldest()->get();
        $tokensIdsToDelete = [];

        if (sizeof($tokens) > env('USER_MAX_TOKEN_COUNT')) {
            for ($i = 0; $i < sizeof($tokens) - env('USER_MAX_TOKEN_COUNT'); $i++) {
                $tokensIdsToDelete[] = $tokens[$i]->id;
            }

            PersonalAccessToken::destroy($tokensIdsToDelete);
        }
    }
}
