<?php

namespace App\Services;

use App\DTO\CreateCommentDTO;
use App\DTO\PatchCommentDTO;
use App\Http\Resources\CreateCommentResource;
use App\Http\Resources\GetCommentsByFilmIdResource;
use App\Models\Comment;
use App\Models\Film;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CommentService
{
    public function getCommentsByFilmId($id): AnonymousResourceCollection
    {
        $film = Film::query()->find($id);
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $comments = Comment::query()
            ->select('comments.id', 'comments.text', 'comments.created_at', 'users.name')
            ->where('film_id', $id)
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->get();

        return GetCommentsByFilmIdResource::collection($comments);
    }

    public function createComment(CreateCommentDTO $dto): CreateCommentResource
    {
        $film = Film::query()->find($dto->getFilmId());
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $comment = Comment::query()->create([
            'text' => $dto->getText(),
            'comment_id' => $dto->getCommentId(),
            'film_id' => $dto->getFilmId(),
            'user_id' => $dto->getUserId(),
        ]);

        return new CreateCommentResource($comment);
    }

    public function patchComment(string $commentId, PatchCommentDTO $dto, string $userRole, string $userId): void
    {
        if($this->checkIsAuthorizedForCommentModeration($userRole, $userId, $commentId)) {
            Comment::query()->where('id', $commentId)->update([
                'text' => $dto->getText(),
            ]);

            return;
        }

        throw new AccessDeniedHttpException('Вы не можете редактировать комментарий, принадлежащий другому пользователю');
    }

    public function deleteComment(string $commentId, string $userRole, string $userId): void
    {
        if($this->checkIsAuthorizedForCommentModeration($userRole, $userId, $commentId)) {
            Comment::query()->where('id', $commentId)->delete();

            return;
        }

        throw new AccessDeniedHttpException('Вы не можете удалить комментарий, принадлежащий другому пользователю');
    }

    private function checkIsAuthorizedForCommentModeration(string $userRole, string $userId, string $commentId): bool {
        if($userRole === 'admin' || $userRole === 'moderator') {
            return true;
        }

        $comment = Comment::query()->findOrFail($commentId);

        if($comment->user_id === $userId) {
            return true;
        }

        return false;
    }
}
