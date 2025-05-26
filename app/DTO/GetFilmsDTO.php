<?php

namespace App\DTO;

class GetFilmsDTO
{
    private int $page = 1;
    private ?string $genre = null;
    private string $status = 'ready';
    private ?string $orderBy = null;
    private string $orderTo = 'asc';

    public function __construct(
        array $data = []
    ) {
        if (isset($data['page'])) {
            $this->setPage($data['page']);
        }

        if (isset($data['genre'])) {
            $this->setGenre($data['genre']);
        }

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }

        if (isset($data['orderBy'])) {
            $this->setOrderBy($data['orderBy']);
        }

        if (isset($data['orderTo'])) {
            $this->setOrderTo($data['orderTo']);
        }
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    public function setOrderTo(string $orderTo): void
    {
        $this->orderTo = $orderTo;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getOrderTo(): string
    {
        return $this->orderTo;
    }
}

