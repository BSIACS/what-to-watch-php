<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    function __construct(
        protected AuthService $authService,
        protected UserService $userService,
    ){}

    public function register(RegisterUserRequest $request): RegisterResource|JsonResponse
    {
        try {
            $registeredUserData = $this->authService->processRegister(
                $request->get('name'),
                $request->get('email'),
                $request->get('password'),
            );
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new RegisterResource($registeredUserData['user'], $registeredUserData['token']);
    }

    public function login(LoginUserRequest $request): LoginResource|JsonResponse
    {
        try {
            if (!Auth::guard('api')->attempt($request->only(['email', 'password']))) {

                return response()->json(['message: Неверный email или пароль'], 401);
            }

            $userData = $this->authService->processLogin($request->get('email'));
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new LoginResource($userData['user'], $userData['token']);
    }

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
