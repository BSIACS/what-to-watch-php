<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatchUserRequest;
use App\Http\Resources\GetUserResource;
use App\Services\FileStorageService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    function __construct(protected UserService $userService, protected FileStorageService $fileStorageService)
    {
    }

    public function getUser()
    {
        $user = Auth::user();

        return new GetUserResource($user);
    }

    public function patchUser(PatchUserRequest $request): GetUserResource|JsonResponse
    {
        try {
            $user = Auth::user();

            if ($request->hasFile('file')) {
                $this->fileStorageService->replaceUserAvatar(
                    $user->id,
                    $request->file('file'),
                    $user->avatar_path
                );
            }

            $patchedUser = $this->userService->patch($user->id, $request->validated());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new GetUserResource($patchedUser);
    }
}
