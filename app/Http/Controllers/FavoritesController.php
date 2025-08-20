<?php

namespace App\Http\Controllers;


use App\Http\Resources\FilmShortCollection;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use OpenApi\Attributes as OA;


class FavoritesController extends Controller
{
    function __construct(protected FavoriteService $favoriteService){ }

    #[OA\Get(
        path: '/api/favorite',
        description: 'Предоставляет список фильмов добавленных пользователем в избранное.',
        summary: 'Получение списка избранных фильмов',
        security: [["sanctumAuth" => []]],
        tags: ['Favorites'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список фильмов добавленных в избранное',
                content: new OA\JsonContent(ref: '#/components/schemas/FilmShortCollection')),

            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с предоствленным id не существует')],
                    type: 'object')),
        ]
    )]
    public function getFavorites():  FilmShortCollection | JsonResponse
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

    #[OA\Post(
        path: '/api/films/{id}/favorite',
        description: 'Добавление фильма в избранное.',
        summary: 'Добавление фильма в избранное',
        security: [["sanctumAuth" => []]],
        tags: ['Favorites'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Идентификатор фильма', in: 'path', example: '010a26e3-b835-4611-bfe9-d7bba5324417'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Фильм добавлен в избранное',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм добавлен в избранное')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с предоствленным id не существует')],
                    type: 'object')),
        ]
    )]
    public function addToFavorites(string $id): JsonResponse
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

    #[OA\Delete(
        path: '/api/films/{id}/favorite',
        description: 'Удаление фильма из списка избранных.',
        summary: 'Удаление фильма из списка избранных',
        security: [["sanctumAuth" => []]],
        tags: ['Favorites'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Идентификатор фильма', in: 'path', example: '010a26e3-b835-4611-bfe9-d7bba5324417'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Фильм удален из списка избранных',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм удален из списка избранных')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с предоствленным id не существует')],
                    type: 'object')),
        ]
    )]
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
