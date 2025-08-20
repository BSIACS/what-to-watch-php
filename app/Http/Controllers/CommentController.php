<?php

namespace App\Http\Controllers;

use App\DTO\CreateCommentDTO;
use App\DTO\PatchCommentDTO;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\PatchCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Attributes as OA;


class CommentController extends Controller
{
    function __construct(protected CommentService $commentService) { }

    #[OA\Get(
        path: '/api/films/{id}/comments',
        description: 'Предоставляет список отзывов к фильму по идентификатору фильма.',
        summary: 'Получение списка отзывов к фильму',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(description: 'Film id', type: 'string', example: '010a26e3-b835-4611-bfe9-d7bba5324417')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список комментариев',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentCollection')),
            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Фильм с предоствленным id не существует')],
                    type: 'object'))
        ]
    )]
    public function getCommentsByFilmId($id): CommentCollection | JsonResponse
    {
        try {
            $resource = $this->commentService->getCommentsByFilmId($id);
        }
        catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    #[OA\Post(
        path: '/api/films/{id}/comments',
        description: 'Добавление отзыва к фильму по идентификатору фильма. В качестве параметра в адресе указывается id фильма к которому добавляется комментарий.
                      Комментарий может быть добавлен отдельно, так и в ответ на другой, в этом случае в теле запроса указывается comment_id.',
        summary: 'Добавление отзыва к фильму',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/CreateCommentRequest'),
            )),
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Film id', in: 'path', schema: new OA\Schema(type: 'string', example: '010a26e3-b835-4611-bfe9-d7bba5324416')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Комментарий успешно создан',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'data', ref: '#/components/schemas/CommentResource')],
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
    public function createComment(CreateCommentRequest $request, $id): CommentResource | JsonResponse
    {
        try {
            $user = Auth::user();
            $resource = $this->commentService->createComment(new CreateCommentDTO([
                'filmId' => $id,
                'text' => $request->get('text'),
                'commentId' => $request->get('comment_id'),
                'userId' => $user->id,
            ]));
        }
        catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    #[OA\Post(
        path: '/api/comments/{id}',
        description: 'Редактирование комментария по идентификатору фильма. Авторизация: обладатель роли "user" - может отредактировать только свой комментарий; обладатель роли "moderator" - может отредактировать любой комментарий.',
        summary: 'Редактирование комментария',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/PatchCommentRequest'),
            )),
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Comment id', in: 'path', schema: new OA\Schema(type: 'string', default: '010a26e3-b835-4611-bfe9-d7bba5324416')),
            new OA\Parameter(name: '_method', in: 'query', example: 'PATCH'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Комментарий успешно отредактирован',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий успешно отредактирован')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Комментарий с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий с предоствленным id не существует')],
                    type: 'object'))
        ]
    )]
    public function patchComment(PatchCommentRequest $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $this->commentService->patchComment(
                $id,
                new PatchCommentDTO(['text' => $request->get('text'), ]),
                $user->role->name,
                $user->id
            );
        }
        catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            if($exception instanceof AccessDeniedHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 403);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Комментарий успешно отредактирован'], 200);
    }

    #[OA\Delete(
        path: '/api/comments/{id}',
        description: 'Выполняет удаление комментария по его идентификатору. Авторизация: обладатель роли "user" - может удалить только свой комментарий; обладатель роли "moderator" - может удалить любой комментарий.',
        summary: 'Удаление комментария',
        security: [["sanctumAuth" => []]],
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Comment id', in: 'path', schema: new OA\Schema(type: 'string', default: '010a26e3-b835-4611-bfe9-d7bba5324416')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Комментарий успешно удалён',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий успешно удалён')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Комментарий с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий с предоствленным id не существует')],
                    type: 'object'))
        ]
    )]
    public function deleteComment($id): JsonResponse
    {
        try {
            $user = Auth::user();

            $this->commentService->deleteComment(
                $id,
                $user->role->name,
                $user->id
            );
        }
        catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            if($exception instanceof AccessDeniedHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 403);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Комментарий успешно удалён'], 200);
    }
}
