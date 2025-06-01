<?php

namespace App\DTO;

class PatchCommentDTO
{
    private string $text;


    public function __construct(
        array $data = []
    ) {
        if (isset($data['text'])) {
            $this->setText($data['text']);
        }
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}

