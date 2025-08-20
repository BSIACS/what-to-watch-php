<?php

namespace App\DTO;

class PatchFilmDTO
{
    private ?string $name = null;
    private ?string $backgroundColor = null;
    private ?int $released = null;
    private ?string $description = null;
    private ?string $director = null;
    private ?array $starring = null;
    private ?int $runtime = null;
    private ?string $imdbId = null;
    private ?string $genres = null;
    private ?string $status = null;


    public function __construct(
        array $data = []
    ) {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['backgroundColor'])) {
            $this->setBackgroundColor($data['backgroundColor']);
        }

        if (isset($data['released'])) {
            $this->setReleased($data['released']);
        }

        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (isset($data['director'])) {
            $this->setDirector($data['director']);
        }

        if (isset($data['starring'])) {
            $this->setStarring($data['starring']);
        }

        if (isset($data['runtime'])) {
            $this->setRuntime($data['runtime']);
        }

        if (isset($data['imdbId'])) {
            $this->setImdbId($data['imdbId']);
        }

        if (isset($data['genres'])) {
            $this->setGenres($data['genres']);
        }

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setBackgroundColor(string $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function setReleased(int $released): void
    {
        $this->released = $released;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setDirector(string $director): void
    {
        $this->director = $director;
    }

    public function setStarring(array $starring): void
    {
        $this->starring = $starring;
    }

    public function setRuntime(int $runtime): void
    {
        $this->runtime = $runtime;
    }

    public function setImdbId(string $imdbId): void
    {
        $this->imdbId = $imdbId;
    }

    public  function setGenres(string $genres): void
    {
        $this->genres = $genres;
    }

    public  function setStatus(string $statusId): void
    {
        $this->status = $statusId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function getReleased(): ?int
    {
        return $this->released;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function getStarring(): ?array
    {
        return $this->starring;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function getGenres(): ?string
    {
        return $this->genres;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function fromRequest() : array
    {
        $result = collect([
            'name' => $this->name,
            'background_color' => $this->backgroundColor,
            'released' => $this->released,
            'description' => $this->description,
            'director' => $this->director,
            'starring' => $this->starring !== null ? implode(', ', $this->starring) : null,
            'run_time' => $this->runtime,
        ])->filter()->toArray();

        return $result;
    }
}

