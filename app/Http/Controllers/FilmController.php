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
    )
    {
    }

    #[OA\Get(
        path: '/api/films',
        description: 'Provides information about the first 8 films unless otherwise specified (the page parameter).',
        summary: 'Get a list of films',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'genre', description: 'Genre name to filter by e. g.: Drama, Action, Comedy, etc.', in: 'query', schema: new OA\Schema(type: 'string')),
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
        description: 'Provides film info by id.',
        summary: 'Provides film info by id',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'string', default: '010a26e3-b835-4611-bfe9-d7bba5324416')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Film detail info',
                content: new OA\JsonContent(ref: '#/components/schemas/FilmResource')),
            new OA\Response(
                response: 404,
                description: 'Film with the requested ID does not exist')
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
        description: 'The method returns a list of 4 matching movies. Similarity is determined by belonging to the same genres as the original movie (any of the available ones).',
        summary: 'Get a list of similar films',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path'),
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/FilmShortCollection')),
            new OA\Response(
                response: 404,
                description: 'Film with specified id not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильма с таким id не существует')],
                    type: 'object')
            )
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
        description: 'Create film by imdbId. To obtain some of the information, an external service API is used (omdbapi.com). Authorization: only for moderator',
        summary: 'Create film by imdbId',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CreateFilmRequest')),
        tags: ['Films'], responses: [new OA\Response(response: 200, description: 'Job was successfully created', content: new OA\JsonContent())],
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
        description: 'Update film info according to the submitted form data. Authorization: only for moderator',
        summary: 'Patch film by id',
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
                description: 'Film data was successfully patched',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Данные фильма успешно отредактированы')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Film with specified id not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильма с таким id не существует')],
                    type: 'object')
            )
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
