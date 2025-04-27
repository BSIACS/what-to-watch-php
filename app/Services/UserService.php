<?php

namespace App\Services;

use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    function __construct()
    {
    }

    public function getById($id): User|Builder
    {
        return User::query()->find($id);
    }

    public function register(string $name, string $email, string $password): array
    {
        $role = Role::query()->where('name', 'user')->first();

        DB::beginTransaction();

        $registeredUser = User::query()->create([
            "id" => Str::uuid(),
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $role->id,
        ]);

        $token = $registeredUser->createToken("Token of user: {$registeredUser->name}");

        DB::commit();

        return [
            'user' => $registeredUser,
            'token' => $token->plainTextToken,
        ];
    }

    public function patch(string $id, mixed $patchData): User|Builder
    {
        $foundUser = User::query()->where('id', $id)->first();
        $foundUser->update($patchData);

        return $foundUser;
    }

    public function createToken(string $email)//: array
    {
        $user = User::query()->where('email', $email)->first();

        $tokens = PersonalAccessToken::query()->where('tokenable_id', $user->id)->oldest()->get();
        $tokensIdsToDelete = [];

        if (sizeof($tokens) >= env('USER_MAX_TOKEN_COUNT')) {
            for ($i = 0; $i <= sizeof($tokens) - env('USER_MAX_TOKEN_COUNT'); $i++) {
                $tokensIdsToDelete[] = $tokens[$i]->id;
            }

            PersonalAccessToken::destroy($tokensIdsToDelete);
        }
        $token = $user->createToken("Token of user: {$user->name}");

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
}
