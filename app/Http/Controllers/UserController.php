<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatchUserRequest;
use App\Http\Requests\SaveOrReplaceUserAvatarRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserAvatarStorageService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;


class UserController extends Controller
{
    function __construct(
        protected UserService $userService,
        protected UserAvatarStorageService $userAvatarStorageService) {}

    #[OA\Get(
        path: '/api/user',
        description: 'Предоставляет данные о профиля пользователя',
        summary: 'Получение профиля пользователя',
        security: [["sanctumAuth" => []]],
        tags: ['User'],
        responses: [new OA\Response(
            response: 200,
            description: 'Данные профиля пользователя',
            content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')],
                type: 'object'
            ))]
    )]
    public function getUser(): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }

    #[OA\Post(
        path: '/api/user',
        description: 'Обновляет данные профиля пользователя',
        summary: 'Обновление профиля пользователя',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/PatchUserRequest'),
            )),
        tags: ['User'],
        parameters: [
            new OA\Parameter(name: '_method', in: 'query', example: 'PATCH'),
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'Данные профиля пользователя',
            content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/UserResource')],
                type: 'object'
            ))]
    )]
    public function patchUser(PatchUserRequest $request): UserResource | JsonResponse
    {
        try {
            $user = Auth::user();

            $patchedUser = $this->userService->patch($user->id, $request->validated());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new UserResource($patchedUser);
    }

    #[OA\Post(
        path: '/api/user/avatar',
        description: 'Сохраненяет аватар пользователя',
        summary: 'Сохранение аватара пользователя',
        security: [["sanctumAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/SaveOrReplaceUserAvatarRequest'),
            )),
        tags: ['User'],
        responses: [new OA\Response(
                response: 200,
                description: 'Путь к аватару пользователя',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'data', ref: '#/components/schemas/AvatarPathResource')],
                    type: 'object'
            ))]
    )]
    public function saveOrReplaceUserAvatar(SaveOrReplaceUserAvatarRequest $request): JsonResponse
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

        return new JsonResponse(['avatarPath' => $filePath], 200);
    }
}
