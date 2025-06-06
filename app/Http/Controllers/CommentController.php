<?php

namespace App\Http\Controllers;

use App\DTO\CreateCommentDTO;
use App\DTO\PatchCommentDTO;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\PatchCommentRequest;
use App\Http\Resources\CreateCommentResource;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CommentController extends Controller
{
    function __construct(protected CommentService $commentService) { }

    public function getCommentsByFilmId($id): AnonymousResourceCollection | JsonResponse
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

    public function createComment(CreateCommentRequest $request, $id): CreateCommentResource | JsonResponse
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
