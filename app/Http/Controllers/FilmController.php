<?php

namespace App\Http\Controllers;


use App\DTO\GetFilmsDTO;
use App\DTO\PatchFilmDTO;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\GetFilmsRequest;
use App\Http\Requests\PatchFilmRequest;
use App\Http\Resources\FilmShortCollection;
use App\Http\Resources\PaginatedFilmShortCollection;
use App\Http\Resources\FilmResource;
use App\Jobs\CreateFilm;
use App\Services\FilmService;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use OpenApi\Attributes as OA;


class FilmController extends Controller
{
    function __construct(
        protected GenreService $genreService,
        protected FilmService  $filmService,
    ) { }

    #[OA\Get(
        path: '/api/films',
        description: 'Возвращает первые 8 фильмов, если не передано другое условие (параметр page).
                    Сортировка по дате выхода и рейтингу фильма.
                    По умолчанию фильмы сортируются по дате выхода, от новых к старым (desc).
                    Фильтрация по жанрам и статусу.',
        summary: 'Получение списка фильмов',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'genre', description: 'Название жанра: Drama, Action, Comedy, и т. д.', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(enum: ['ready','pending','on_moderation'])),
            new OA\Parameter(name: 'order_by', in: 'query', schema: new OA\Schema(enum: ['released','rating'], )),
            new OA\Parameter(name: 'order_to', in: 'query', schema: new OA\Schema(enum: ['asc','desc'])),
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/PaginatedFilmShortCollection'))]
    )]
    public function getFilms(GetFilmsRequest $request): JsonResponse | PaginatedFilmShortCollection
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

    #[OA\Get(
        path: '/api/films/{id}',
        description: 'Предоставляет информацию о фильме по его идентификатору.',
        summary: 'Получение информации о фильме',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', default: '010a26e3-b835-4611-bfe9-d7bba5324416')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Подробная информация о фильме',
                content: new OA\JsonContent(ref: '#/components/schemas/FilmResource')),
            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с предоствленным id не существует')],
                    type: 'object')),
        ],
    )]
    public function getFilmById($id): FilmResource | JsonResponse
    {
        try {
            $resource = $this->filmService->getFilmById($id);
        } catch (\Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    #[OA\Get(
        path: '/api/films/{id}/similar',
        description: 'Предоставляет список из 4 похожих фильмов.
                    Похожесть определяется принадлежностью к тем же жанрам, что и исходный фильм (любым из имеющихся).',
        summary: 'Получение списка похожих фильмов',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of films',
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
        ],
    )]
    public function getSimilarFilms(string $id): FilmShortCollection | JsonResponse
    {
        try {
            $resource = $this->filmService->getSimilarFilms($id);
        } catch (\Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    #[OA\Post(
        path: '/api/films',
        description: 'Создаёт фильм в базе по его imdbId. Для получения начальных данных используется внешний API (omdbapi.com). Авторизация: только для пользователей с ролью "moderator"',
        summary: 'Создание фильма в базе по его imdbId',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CreateFilmRequest')),
        tags: ['Films'], responses: [new OA\Response(response: 200, description: 'Задача на добавление фильма в базу данных успешно создана', content: new OA\JsonContent())],
    )]
    public function createFilm(CreateFilmRequest $request): JsonResponse
    {
        try {
            $this->filmService->createFilm('tt0120586');
        } catch (\Exception $exception) {
            if ($exception instanceof UnprocessableEntityHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 422);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        CreateFilm::dispatch($request->imdbId);

        return new JsonResponse(['message' => 'Задача на добавление фильма в базу данных успешно создана'], 200);
    }

    #[OA\Post(
        path: '/api/films/{id}',
        description: 'Редактирование данных о фильме. Авторизация: только для пользователей с ролью "moderator"',
        summary: 'Редактирование фильма ',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/PatchFilmRequest'),
                encoding: ['starring[]' => [
                    'mediaType' => 'multipart/form-data',
                    'style' => 'form',
                    'explode' => true
                ]]
            )),
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Film id', in: 'path', example: '9f3d5512-deac-43d6-bd9f-42d7eaf6b4cf'),
            new OA\Parameter(name: '_method', in: 'query', example: 'PATCH'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные фильма успешно отредактированы',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Данные фильма успешно отредактированы')],
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
        ],
    )]
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
        } catch (\Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Данные фильма успешно отредактированы'], 200);
    }
}
