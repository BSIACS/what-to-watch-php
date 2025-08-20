<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GenreService
{
    public function getById($id): \Illuminate\Database\Eloquent\Model
    {
        return User::query()->find($id);
    }

    public function patch($id, $patchData): void
    {
        $genre = Genre::query()->find($id);
        $genre ?? throw new NotFoundHttpException('Жанр с предоствленным id не существует');

        $genre->update($patchData);
    }
}
