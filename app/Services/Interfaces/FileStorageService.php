<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileStorageService
{
    public function saveFile(UploadedFile $file, string $catalogPath): string;

    public function deleteFile(string $filePath): void;
}
