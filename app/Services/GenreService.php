<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenreService
{
    public function getById($id): \Illuminate\Database\Eloquent\Model
    {
        return User::query()->find($id);
    }

    public function patch($id, $patchData)
    {
        $genre = Genre::query()->whereId($id)->firstOrFail();
        $genre->update($patchData);

        return $genre;
    }
}
