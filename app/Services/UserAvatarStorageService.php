<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isNull;

class UserAvatarStorageService
{
    /**
     * Сохраняет или заменяет аватар пользователя в публичном хранилище. Где путь директории {Id пользователя}/avatar/{сгенерированное имя файла}
     *
     * @param string $userId id пользователя
     * @param string|null $currentAvatarPath путь к имеющемуся аватару пользователя
     * @param UploadedFile $file загружаемый файл
     * @return string
     * @throws Exception
     */
    public function saveOrReplaceUserAvatar(string $userId, string|null $currentAvatarPath, UploadedFile $file): string
    {
        if($currentAvatarPath !== null) {
            $this->deleteUserAvatar($currentAvatarPath);
        }

        return $this->saveUserAvatar($file, $userId);
    }

    /**
     * Сохраняет аватар пользователя в публичное хранилище. Где путь директории {Id пользователя}/avatar/{сгенерированное имя файла}
     *
     * @param UploadedFile $file загружаемый файл
     * @param string $userId id пользователя
     * @return string
     * @throws Exception
     */
    public function saveUserAvatar(UploadedFile $file, string $userId): string
    {
        $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $filePath = $userId . '/avatar/' . $fileName;

        Storage::disk('public')->makeDirectory($userId);
        $isMakeDirectorySuccess = Storage::disk('public')->exists($userId);
        if(!$isMakeDirectorySuccess) {
            throw new Exception('File storage directory not created.');
        }

        Storage::disk('public')->put($filePath, file_get_contents($file));
        $isPutFileSuccess = Storage::disk('public')->exists($filePath);
        if(!$isPutFileSuccess) {
            throw new Exception('File not saved.');
        }

        return $filePath;
    }

    /**
     * Удаляет аватар пользователя по адресу в файловой системе.
     *
     * @param string $filePath путь к файлу
     */
    public function deleteUserAvatar(string $filePath): void
    {
        Storage::disk('public')->delete($filePath);
    }
}
