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
        description: 'Get user data',
        summary: 'Get user data',
        security: [["sanctumAuth" => []]],
        tags: ['User'],
        parameters: [
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/UserResource'))]
    )]
    public function getUser(): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }

    #[OA\Post(
        path: '/api/user',
        description: 'Get user data',
        summary: 'Get user data',
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
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/UserResource'))]
    )]
    public function patchUser(PatchUserRequest $request): UserResource|JsonResponse
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
        description: 'Save user avatar',
        summary: 'Save user avatar',
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
                description: 'Film with specified id not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'avatarPath',
                        type: 'string',
                        example: 'ef7d1976-5cd5-4c99-9bdf-cbd2209f214e/avatar/KgoFL8KpEtajLXJ225JjcMBIbMlKbXVd.jpg')],
                    type: 'object')
        )]
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
