<?php

namespace App\Http\Controllers;


use App\DTO\GetFilmsDTO;
use App\Http\Requests\GetFilmsRequest;
use App\Http\Resources\GetFilmByIdResource;
use App\Services\FilmService;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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
}
