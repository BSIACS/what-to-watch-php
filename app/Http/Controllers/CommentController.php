<?php

namespace App\Http\Controllers;

use App\Constants\SwaggerConstants;
use App\DTO\CreateCommentDTO;
use App\DTO\PatchCommentDTO;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\PatchCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CreateCommentResource;
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
        description: 'Returns comments to a film by film id.',
        summary: 'Get a list of comments',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(description: 'Film id', type: 'string', example: '010a26e3-b835-4611-bfe9-d7bba5324417')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of comments',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentCollection')),
            new OA\Response(
                response: 404,
                description: 'Film with the requested id does not exist',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Фильм с таким id не существует')],
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
        description: 'Create comment.',
        summary: 'Create comment',
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
                description: 'Comment created successfully',
                content: new OA\JsonContent(
                    properties: [new OA\Property(ref: '#/components/schemas/CommentResource')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Film with specified id not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с таким id не существует')],
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
        description: 'Patch comment by id. Authorization: with "user" role - can only patch his own comment; with "moderator" role - can patch any comment.',
        summary: 'Patch comment by id',
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
                description: 'Comment patched successfully',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий успешно отредактирован')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Comment with requested id not exist',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий с таким id не существует')],
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
        description: 'Delete comment by id. Authorization: with "user" role - can only delete his own comment; with "moderator" role - can delete any comment.',
        summary: 'Delete comment by id',
        security: [["sanctumAuth" => []]],
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Comment id', in: 'path', schema: new OA\Schema(type: 'string', default: '010a26e3-b835-4611-bfe9-d7bba5324416')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comment deleted successfully',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий успешно удалён')],
                    type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Comment with requested id not exist',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Комментарий с таким id не существует')],
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
