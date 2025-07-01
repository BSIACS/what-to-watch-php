<?php

namespace App\Services;


use App\Services\Interfaces\FileStorageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Error;

class LocalFileStorageService implements FileStorageService
{
    /**
     * @throws Exception
     */
    public function saveFile(UploadedFile $file, string $catalogPath): string
    {
        $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $filePath = $catalogPath . '/' . $fileName;

        try {
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $isPutFileSuccess = Storage::disk('public')->exists($filePath);
            if(!$isPutFileSuccess) {
                throw new Error("ошибка проверки существования записываемого файла (метод exists($filePath) вернул false)");
            }
        }
        catch (Exception $exception) {
            Log::error('Ошибка записи файла в каталог ' . $catalogPath . ': ' . $exception->getMessage());
            throw new Error("Ошибка записи файла");
        }

        return $filePath;
    }

    public function deleteFile(string $filePath): void
    {
        Storage::disk('public')->delete($filePath);
    }
}
