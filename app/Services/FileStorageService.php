<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Сохраняет аватар пользователя в публичное хранилище. Где путь директории {Id пользователя}/avatar/{сгенерированное имя файла}
     *
     * @param UploadedFile $file загружаемый файл
     * @param string $userId id пользователя
     * @return bool
     */
    public function saveUserAvatar(UploadedFile $file, string $userId): bool
    {
        try {
            $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();

            Storage::disk('public')->makeDirectory($userId);
            Storage::disk('public')->put($userId . '/avatar/' . $fileName, file_get_contents($file));

            User::query()->find($userId)->update(['avatar_path' => $fileName]);
        }
        catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function replaceUserAvatar(string $userId, UploadedFile $file, string $avatarFileNameToReplace = null): bool
    {
        try {
            if ($avatarFileNameToReplace) {
                $this->deleteUserAvatar($userId, $avatarFileNameToReplace);
            }

            $this->saveUserAvatar($file, $userId);
        }
        catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function deleteUserAvatar(string $userId, string $fileToDelete): bool
    {
        try {
            Storage::disk('public')->delete($userId . '/avatar/' . $fileToDelete);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }
}
