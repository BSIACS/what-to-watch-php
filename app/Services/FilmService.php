<?php

namespace App\Services;

use App\DTO\GetFilmsDTO;
use App\Http\Resources\GetFilmByIdResource;
use App\Http\Resources\GetFilmsResource;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class FilmService
{
    public function getFilms(GetFilmsDTO $dto): AnonymousResourceCollection
    {
        $genre = $dto->getGenre();
        $filmsPerPage = 4;
        $skipCount = $filmsPerPage * ($dto->getPage() - 1);

        $films = Film::query()
            ->whereHas('genres', function($query) use ($genre) {
                if(isset($genre)) {
                    $query->where('name', '=', $genre);
                }
            })
            ->select(
                'films.id',
                'films.name',
                'films.preview_image',
                'films.preview_video_link',
                'film_statuses.name as status',
                'films.released',
                DB::raw('rating / IF(score_count = 0, 1, score_count) AS rating')
            )
            ->join('film_statuses', 'films.status_id',  '=', 'film_statuses.id')
            ->where('film_statuses.name', '=', $dto->getStatus())
            ->orderBy($dto->getOrderBy(), $dto->getOrderTo())
            ->skip($skipCount)
            ->take($filmsPerPage)
            ->get();


        return GetFilmsResource::collection($films)
            ->additional([
                'current_page' => $dto->getPage(),
                'per_page' => $filmsPerPage,
                'total_pages' => $this->getPageCountByFilters($filmsPerPage, $dto->getGenre(), $dto->getStatus()),
            ]);
    }

    private function getPageCountByFilters($filmsPerPage, $genre, $status): int
    {
        $filmCount = Film::query()
            ->whereHas('genres', function($query) use ($genre) {
                if(isset($genre)) {
                    $query->where('name', '=', $genre);
                }
            })
            ->select(
                'films.id',
                'films.name',
                'films.preview_image',
                'films.preview_video_link',
                'film_statuses.name as status',
                'films.released',
                DB::raw('rating / IF(score_count = 0, 1, score_count) AS rating')
            )
            ->join('film_statuses', 'films.status_id',  '=', 'film_statuses.id')
            ->where('film_statuses.name', '=', $status)
            ->count();

        return (int) ceil($filmCount / $filmsPerPage);
    }

    public function getFilmById(string $id): GetFilmByIdResource
    {
        $film = Film::query()
            ->with('genres')
            ->find($id);
        if($film === null) {
            throw new NotFoundHttpException('Фильм с id = ' . $id . ' не найден');
        }
        $genres = [];
        foreach ($film->genres as $genre) {
            $genres[] = $genre->name;
        }

        return  new GetFilmByIdResource($film, $genres);
    }

    public function getSimilarFilms(string $id): AnonymousResourceCollection
    {
        $film = Film::query()->find($id) ?? throw new NotFoundHttpException('Фильма с таким id не существует');

        $genres = $film->genres()->pluck('id')->toArray();

        $films = Film::query()
            ->select('films.name',
                'films.id',
                'films.preview_image',
                'films.preview_video_link',
                'film_statuses.name as status',
                'films.released',
                DB::raw('rating / IF(score_count = 0, 1, score_count) AS rating')
            )
            ->join('film_genre', 'films.id', '=', 'film_genre.film_id')
            ->join('film_statuses', 'films.status_id',  '=', 'film_statuses.id')
            ->whereIn('genre_id', $genres)
            ->groupBy('films.id')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return GetFilmsResource::collection($films);
    }
}
