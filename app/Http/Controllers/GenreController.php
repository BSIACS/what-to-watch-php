<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreCollection;
use App\Http\Resources\UpdateGenresResource;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class GenreController extends Controller
{
    function __construct(protected GenreService $genreService){ }

    #[OA\Get(
        path: '/api/genre',
        description: 'Маршрут предоставляет список доступных жанров.',
        summary: 'Получить список жанров',
        tags: ['Genres'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список жанров',
                content: new OA\JsonContent(ref: '#/components/schemas/GenreCollection')),
        ]
    )]
    public function getAll(): GenreCollection | JsonResponse
    {
        try {
            $genres = Genre::query()->get();
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new GenreCollection($genres);
    }

    #[OA\Post(
        path: '/api/genre/{id}',
        description: 'Маршрут обновляет данные о жанре. Авторизация: доступен только пользователям с ролью "moderator"',
        summary: 'Обновить данные о жанре',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/UpdateGenreRequest'),
            )),
        tags: ['Genres'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Идентификатор жанра', in: 'path', example: '010a26e3-b835-4611-bfe9-d7bba5324417'),
            new OA\Parameter(name: '_method', in: 'query', example: 'PATCH'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные жанра успешно отредактированы',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Данные жанра успешно отредактированы')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Жанр с таким id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Жанр с предоствленным id не существует')],
                    type: 'object')
            )
        ],
    )]
    public function update($id, UpdateGenreRequest $request): UpdateGenresResource | JsonResponse
    {
        try {
            $this->genreService->patch($id, $request->validated());
        } catch (\Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Данные жанра успешно отредактированы'], 200);
    }
}
