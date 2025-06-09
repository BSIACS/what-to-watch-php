<?php

namespace App\Http\Controllers;


use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class FavoritesController extends Controller
{
    function __construct(protected FavoriteService $favoriteService){ }

    public function getFavorites(): JsonResponse | AnonymousResourceCollection
    {
        try {
            $user = Auth::user();
            $resource = $this->favoriteService->getFavorites($user->id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    public function addToFavorites(string $id)
    {
        try {
            $user = Auth::user();
            $this->favoriteService->addToFavorites($id, $user->id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            if($exception instanceof UnprocessableEntityHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 422);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Фильм добавлен в избранное'], 200);
    }

    public function removeFromFavorites(string $id)
    {
        try {
            $user = Auth::user();
            $this->favoriteService->removeFromFavorites($id, $user->id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            if($exception instanceof UnprocessableEntityHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 422);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Фильм удален из избранного'], 200);
    }
}
