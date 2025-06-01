<?php

namespace App\DTO;

class CreateCommentDTO
{
    private string $filmId;
    private string $text;
    private ?string $commentId = null;
    private ?string $userId;


    public function __construct(
        array $data = []
    ) {
        if (isset($data['filmId'])) {
            $this->setFilmId($data['filmId']);
        }

        if (isset($data['text'])) {
            $this->setText($data['text']);
        }

        if (isset($data['commentId'])) {
            $this->setCommentId($data['commentId']);
        }

        if (isset($data['userId'])) {
            $this->setUserId($data['userId']);
        }
    }

    public function setFilmId(string $filmId): void
    {
        $this->filmId = $filmId;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setCommentId(?string $commentId): void
    {
        $this->commentId = $commentId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function getFilmId(): string
    {
        return $this->filmId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCommentId(): ?string
    {
        return $this->commentId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}

