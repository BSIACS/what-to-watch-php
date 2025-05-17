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
    public function getById($id): User|Builder
    {
        return User::query()->find($id);
    }

    public function patch(string $id, mixed $patchData): User|Builder
    {
        $foundUser = User::query()->where('id', $id)->first();
        $foundUser->update($patchData);

        return $foundUser;
    }
}
