<?php

namespace App\Services;


use App\Http\Resources\FilmShortCollection;
use App\Http\Resources\GetFavoritesResource;
use App\Models\Film;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FavoriteService
{
    public function getFavorites(string $id)
    {
        $films = User::query()
            ->select(
                'films.id',
                'films.name',
                'films.preview_image',
                'films.preview_video_link',
                'film_statuses.name as status',
                'films.released',
                DB::raw('rating / IF(score_count = 0, 1, score_count) AS rating')
            )
            ->where('users.id', $id)
            ->join('film_user', 'users.id', '=', 'film_user.user_id')
            ->join('films', 'film_user.film_id', '=', 'films.id')
            ->join('film_statuses', 'films.status_id',  '=', 'film_statuses.id')
            ->get();


//        return GetFavoritesResource::collection($films);
        return new FilmShortCollection($films);

    }

    public function addToFavorites(string $filmId, string $userId): void
    {
        $film = Film::query()->find($filmId);
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $filmUser = User::query()
            ->select(
                'film_user.film_id',
                'film_user.user_id',
            )
            ->join('film_user', 'users.id', '=', 'film_user.user_id')
            ->where([
                ['film_user.film_id', '=', $filmId],
                ['film_user.user_id', '=', $userId]
            ])
            ->first();

        if($filmUser !== null) {
            throw new UnprocessableEntityHttpException('Добавляемый фильм уже находится в списке избранных');
        }

         User::query()->find($userId)->films()->attach($filmId);
    }

    public function removeFromFavorites(string $filmId, string $userId): void
    {
        $film = Film::query()->find($filmId);
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $filmUser = User::query()
            ->select(
                'film_user.film_id',
                'film_user.user_id',
            )
            ->join('film_user', 'users.id', '=', 'film_user.user_id')
            ->where('film_user.film_id', $filmId)
            ->first();

        if($filmUser === null) {
            throw new UnprocessableEntityHttpException('Удаляемый фильм отсутствует в списке избранных');
        }

        User::query()->find($userId)->films()->detach($filmId);
    }
}
