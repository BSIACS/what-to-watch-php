<?php

namespace App\Services;

use App\Constants\PaginationConstants;
use App\DTO\GetFilmsDTO;
use App\DTO\PatchFilmDTO;
use App\Http\Resources\FilmShortCollection;
use App\Http\Resources\PaginatedFilmShortCollection;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\FilmStatus;
use App\Models\Genre;
use App\Services\Interfaces\FileStorageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class FilmService
{
    function __construct(protected FileStorageService $fileStorageService){}

    public function getFilms(GetFilmsDTO $dto): PaginatedFilmShortCollection
    {
        $genre = $dto->getGenre();
        $skipCount = PaginationConstants::FILMS_PER_PAGE * ($dto->getPage() - 1);

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
            ->take(PaginationConstants::FILMS_PER_PAGE)
            ->get();

        return (new PaginatedFilmShortCollection($films))->additional([
                'current_page' => $dto->getPage(),
                'per_page' => PaginationConstants::FILMS_PER_PAGE,
                'total_pages' => $this->getPageCountByFilters($dto->getGenre(), $dto->getStatus()),
            ]);
    }

    private function getPageCountByFilters($genre, $status): int
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

        return (int) ceil($filmCount / PaginationConstants::FILMS_PER_PAGE);
    }

    public function getFilmById(string $id): FilmResource
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

        return new FilmResource($film, $genres);
    }

    public function getSimilarFilms(string $id): FilmShortCollection
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
            ->where('film_statuses.name', '=', 'ready')
            ->groupBy('films.id')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return new FilmShortCollection($films);
    }

    public function createFilm(string $imdbId): void
    {
        if ($this->checkIsExistByImdbId($imdbId)) {
            throw new UnprocessableEntityHttpException('Фильм с таким imdb_id уже существует в базе данных');
        }

        $status = FilmStatus::query()->where('name', '=', 'pending')->first();

        Film::query()->create([
            'imdb_id' => $imdbId,
            'status_id' => $status->id,
            'rating' => 0,
            'score_count' => 0
        ]);
    }

    /**
     * @throws Exception
     */
    public function patchFilmById(
        string $id,
        PatchFilmDTO $dto,
        ?UploadedFile $posterImage,
        ?UploadedFile $previewImage,
        ?UploadedFile $backgroundImage,
        ?UploadedFile $videoLink,
        ?UploadedFile $previewVideoLink
    ): void
    {
        $film = Film::query()->where('id', '=', $id)->first();
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $updateData = $dto->fromRequest();
        if($dto->getStatus()){
            $updateData['status_id'] = FilmStatus::query()->where('name', '=', $dto->getStatus())->first()->id;
        }
        else {
            $updateData['status_id'] = FilmStatus::query()->where('name', '=', 'ready')->first()->id;
        }
        $filesData = [];

        if($posterImage !== null) $filesData[] = ['file' => $posterImage, 'dbColumnName' => 'poster_image', 'catalogName' => 'poster'];
        if($previewImage !== null) $filesData[] = ['file' => $previewImage, 'dbColumnName' => 'preview_image', 'catalogName' => 'preview'];
        if($backgroundImage !== null) $filesData[] = ['file' => $backgroundImage, 'dbColumnName' => 'background_image', 'catalogName' => 'background'];
        if($videoLink !== null) $filesData[] = ['file' => $videoLink, 'dbColumnName' => 'video_link', 'catalogName' => 'video'];
        if($previewVideoLink !== null) $filesData[] = ['file' => $previewVideoLink, 'dbColumnName' => 'preview_video_link', 'catalogName' => 'previewVideo'];

        $oldFilePathsToDelete = [];

        foreach ($filesData as $fileData) {
            $updateData[$fileData['dbColumnName']] = $this->saveNewAndTrackOldFile(
                $fileData['file'],
                $fileData['dbColumnName'],
                $fileData['catalogName'],
                $film[$fileData['dbColumnName']],
                $oldFilePathsToDelete);
        }

        $film->update($updateData);
        $film->save();
        $this->deleteOldFiles($oldFilePathsToDelete);
    }

    private function saveNewAndTrackOldFile(
        UploadedFile $file,
        string $dbColumnName,
        string $catalogName,
        string | null $currentFilePath,
        array &$oldFilePathsToDelete): string
    {
        $newFilePath = $this->fileStorageService->saveFile($file, $catalogName);
        if ($currentFilePath !== null) {
            $oldFilePathsToDelete[$dbColumnName] = $currentFilePath;
        }

        return $newFilePath;
    }

    private function deleteOldFiles(array $oldFilePathsToDelete): void
    {
        foreach ($oldFilePathsToDelete as $oldFileLink) {
            $this->fileStorageService->deleteFile($oldFileLink);
        }
    }

    public function patchFilmByImdbId(PatchFilmDTO $dto, string $imdbId): void
    {
        $film = Film::query()->where('imdb_id', '=', $imdbId)->first();
        $genres = explode(', ', $dto->getGenres());

        $status = FilmStatus::query()->where('name', '=', 'on_moderation')->first();

        $updateData = $dto->fromRequest();
        $updateData['status_id'] = $status->id;

        DB::beginTransaction();

        $film->update($updateData);

        $this->setFilmGenres($genres, $film->id);

        DB::commit();
    }

    private function checkIsExistByImdbId(string $imdbId): bool
    {
        $film = Film::query()
            ->with('genres')
            ->where('imdb_id', '=', $imdbId)
            ->first();

        if($film === null) {
            return false;
        }

        return true;
    }

    private function setFilmGenres(array $genres, string $filmId): void
    {
        foreach ($genres as $genreName) {
            $foundGenre = Genre::query()->where('name', '=', $genreName)->first();

            if($foundGenre === null) {
                $foundGenre = Genre::query()->create([
                    'name' => $genreName,
                ]);
            }

            FilmGenre::query()->create([
                'film_id' => $filmId,
                'genre_id' => $foundGenre->id,
            ]);
        }
    }
}
