<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GetGenresResource;
use App\Http\Resources\UpdateGenresResource;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;


class GenreController extends Controller
{
    function __construct(protected GenreService $genreService){ }

    public function getAll(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection | JsonResponse
    {
        try {
            $genres = Genre::query()->get();
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return GetGenresResource::collection($genres);
    }

    public function update($id, UpdateGenreRequest $request): UpdateGenresResource | JsonResponse
    {
        try {
            $genre = $this->genreService->patch($id, $request->validated());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new UpdateGenresResource($genre);
    }
}
