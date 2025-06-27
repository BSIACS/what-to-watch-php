<?php

namespace App\Http\Controllers;


use App\DTO\GetFilmsDTO;
use App\DTO\PatchFilmDTO;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\GetFilmsRequest;
use App\Http\Requests\PatchFilmRequest;
use App\Http\Resources\GetFilmByIdResource;
use App\Jobs\CreateFilm;
use App\Services\FilmService;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class FilmController extends Controller
{
    function __construct(
        protected GenreService $genreService,
        protected FilmService $filmService,
    ){ }

    public function getFilms(GetFilmsRequest $request): JsonResponse | AnonymousResourceCollection
    {
        try {
            $resource = $this->filmService->getFilms(new GetFilmsDTO(
                [
                    'page' => $request->get('page'),
                    'genre' => $request->get('genre'),
                    'status' => $request->get('status'),
                    'orderBy' => $request->get('order_by'),
                    'orderTo' => $request->get('order_to'),
                ]
            ));
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    public function getFilmById($id): GetFilmByIdResource  | JsonResponse
    {
        try {
            $resource = $this->filmService->getFilmById($id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    public function getSimilarFilms(string $id): JsonResponse | AnonymousResourceCollection
    {
        try {
            $resource = $this->filmService->getSimilarFilms($id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    public function createFilm(CreateFilmRequest $request): JsonResponse
    {
        try {
            $this->filmService->createFilm($request->imdbId);
        } catch (\Exception $exception) {
            if($exception instanceof UnprocessableEntityHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 422);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        CreateFilm::dispatch($request->imdbId);

        return new JsonResponse(['message' => 'Задача на добавление фильма в базу данных успешно создана'], 200);
    }

    public function patchFilm(PatchFilmRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new PatchFilmDTO($request->validated());

            $this->filmService->patchFilmById(
                $id,
                $dto,
                $request->file('posterImage'),
                $request->file('previewImage'),
                $request->file('backgroundImage'),
                $request->file('video'),
                $request->file('previewVideo')
            );
        }
        catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Данные фильма успешно отредактированы'], 200);
    }

    public function setReadyStatus(string $id) {

    }
}
