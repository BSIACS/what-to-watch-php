<?php

namespace App\Services;

use App\DTO\CreateCommentDTO;
use App\DTO\PatchCommentDTO;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Film;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CommentService
{
    public function getCommentsByFilmId($id): CommentCollection
    {
        $film = Film::query()->find($id);
        if($film === null) {
            throw new NotFoundHttpException('Фильма с таким id не существует');
        }

        $comments = Comment::query()
            ->select('comments.id', 'comments.text', 'comments.created_at', 'comments.comment_id', 'users.name')
            ->where('film_id', $id)
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->get();

        return new CommentCollection($comments);
    }

    public function createComment(CreateCommentDTO $dto): CommentResource
    {
        $film = Film::query()->find($dto->getFilmId());
        $film ?? throw new NotFoundHttpException('Фильм с таким id не существует');

        if($dto->getCommentId() !== null) {
            $foundComment = Comment::query()->find($dto->getCommentId());

            $foundComment ?? throw new NotFoundHttpException('Комментарий с таким id не существует');
        }

        $comment = Comment::query()->create([
            'text' => $dto->getText(),
            'comment_id' => $dto->getCommentId(),
            'film_id' => $dto->getFilmId(),
            'user_id' => $dto->getUserId(),
        ]);

        $comment = Comment::query()
            ->select('comments.id', 'comments.text', 'comments.created_at', 'users.name', 'comments.comment_id')
            ->where('comments.id', $comment->id)
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->first();

        return new CommentResource($comment);
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
