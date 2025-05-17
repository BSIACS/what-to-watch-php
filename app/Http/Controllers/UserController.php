<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatchUserRequest;
use App\Http\Requests\SaveOrReplaceUserAvatarRequest;
use App\Http\Resources\GetUserResource;
use App\Models\User;
use App\Services\UserAvatarStorageService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    function __construct(
        protected UserService $userService,
        protected UserAvatarStorageService $userAvatarStorageService) {}

    public function getUser()
    {
        $user = Auth::user();

        return new GetUserResource($user);
    }

    public function patchUser(PatchUserRequest $request): GetUserResource|JsonResponse
    {
        try {
            $user = Auth::user();

            $patchedUser = $this->userService->patch($user->id, $request->validated());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new GetUserResource($patchedUser);
    }

    public function saveOrReplaceUserAvatar(SaveOrReplaceUserAvatarRequest $request)
    {
        $user = Auth::user();

        try {
            $filePath = $this->userAvatarStorageService->saveOrReplaceUserAvatar(
                $user->id,
                $user->avatar_path,
                $request->file('file')
            );

            User::query()->find($user->id)->update(['avatar_path' => $filePath]);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return [
            'avatar_path' => $filePath,
        ];
    }
}
