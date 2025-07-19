<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\TokenResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    function __construct(
        protected AuthService $authService,
        protected UserService $userService,
    ){}

    #[OA\Post(
        path: '/api/register',
        description: 'Process registration',
        summary: 'User registration',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RegisterUserRequest')),
        tags: ['Auth'],
        parameters: [
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/TokenResource'))]
    )]
    public function register(RegisterUserRequest $request): TokenResource | JsonResponse
    {
        try {
            $token = $this->authService->processRegister(
                $request->get('name'),
                $request->get('email'),
                $request->get('password'),
            );
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new TokenResource($token);
    }

    #[OA\Post(
        path: '/api/login',
        description: 'Process login',
        summary: 'User login',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginUserRequest')),
        tags: ['Auth'],
        parameters: [
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'List of films',
            content: new OA\JsonContent(ref: '#/components/schemas/TokenResource'))]
    )]
    public function login(LoginUserRequest $request): TokenResource | JsonResponse
    {
        try {
            if (!Auth::guard('api')->attempt($request->only(['email', 'password']))) {

                return response()->json(['message: Неверный email или пароль'], 401);
            }

            $token = $this->authService->processLogin($request->get('email'));
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new TokenResource($token);
    }

    #[OA\Post(
        path: '/api/logout',
        description: 'Process logout',
        summary: 'User logout',
        security: [["sanctumAuth" => []]],
        tags: ['Auth'],
        parameters: [
        ],
        responses: [new OA\Response(
            response: 200,
            description: 'Successful logout',
            content: new OA\JsonContent(
                properties: [new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Токены удалены')],
                type: 'object'))]
    )]
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->authService->processLogout($user);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Токены удалены'], 200);
    }
}
