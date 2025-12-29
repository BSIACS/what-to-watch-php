<?php

namespace App\Http\Controllers;

use App\Exceptions\EmailAlreadyVerifiedException;
use App\Exceptions\ExpiredVerificationException;
use App\Exceptions\InvalidVerificationException;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\TokenResource;
use App\Services\AuthService;
use App\Services\Interfaces\EmailVerificationServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    function __construct(
        protected AuthService $authService,
        protected UserServiceInterface $userService,
        protected EmailVerificationServiceInterface $emailVerificationService
    ){}

    #[OA\Post(
        path: '/api/register',
        description: 'Выполняет регистрацию. Пользователю присваивается роль "user".',
        summary: 'Регистрация нового пользователя',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RegisterUserRequest')),
        tags: ['Auth'],
        parameters: [
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешная регистрация',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Проверьте вашу почту для подтверждения регистрации')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Поле должно содержать валидный email адрес (and 1 more error)'),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(properties: [
                                new OA\Property('email', example: 'Поле должно содержать валидный email адрес'),
                                new OA\Property('password', example: 'Поле password должно содержать не менее 6 символов')
                            ])
                        )
                    ],
                    type: 'object')
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Внутренняя ошибка сервера')
                    ],
                    type: 'object')
            ),
        ]
    )]
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->processRegister(
                $request->get('name'),
                $request->get('email'),
                $request->get('password'),
            );

            $this->emailVerificationService->initiateEmailVerification(
                $user['id'],
                $user['name'],
                $user['email']
            );
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => 'Внутренняя ошибка сервера'], 500);
        }

        return new JsonResponse(['message' => 'Проверьте вашу почту для подтверждения регистрации'], 200);
    }

    #[OA\Post(
        path: '/api/login',
        description: 'Аутентификация пользователя по email и паролю. Возвращает токен доступа.',
        summary: 'Аутентификация пользователя',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginUserRequest')),
        tags: ['Auth'],
        parameters: [
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешная аутентификация',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/TokenResource'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Неверные учетные данные',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Неверный email или пароль'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Поле должно содержать валидный email адрес (and 1 more error)'),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(properties: [
                                new OA\Property('email', example: 'Поле должно содержать валидный email адрес'),
                                new OA\Property('password', example: 'Поле password должно содержать не менее 6 символов')
                            ])
                        )
                    ],
                    type: 'object')
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Внутренняя ошибка сервера')
                    ],
                    type: 'object')
            )
        ]
    )]
    public function login(LoginUserRequest $request): TokenResource | JsonResponse
    {
        try {
            if (!Auth::guard('api')->attempt($request->only(['email', 'password']))) {

                return response()->json(['message: Неверный email или пароль'], 401);
            }

            $token = $this->authService->processLogin($request->get('email'));
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => 'Внутренняя ошибка сервера'], 500);
        }

        return new TokenResource($token);
    }

    #[OA\Post(
        path: '/api/logout',
        description: 'Удаляет токены доступа',
        summary: 'Выход из системы',
        security: [["sanctumAuth" => []]],
        tags: ['Auth'],
        parameters: [
        ],
        responses: [
            new OA\Response(
            response: 200,
            description: 'Successful logout',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Токены удалены')
                ],
                type: 'object')
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Внутренняя ошибка сервера')
                    ],
                    type: 'object')
            )
        ]
    )]
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->authService->processLogout($user);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => 'Внутренняя ошибка сервера'], 500);
        }

        return new JsonResponse(['message' => 'Токены удалены'], 200);
    }


    public function verifyEmail(Request $request)
    {
        try {
            $fullUrl = $request->fullUrl();

            $this->emailVerificationService->verifyAndConfirmEmail($fullUrl);

        } catch (\Throwable $exception) {
            if ($exception instanceof InvalidVerificationException) {
                return new JsonResponse(['message' => 'Некорректный линк'], 500);
            }
            if ($exception instanceof ExpiredVerificationException) {
                return new JsonResponse(['message' => 'Срок действия линка истёк'], 500);
            }
        }

        return new JsonResponse(['message' => 'Подтверждено успешно'], 200);
    }

    #[OA\Post(
        path: '/api/email/resend-verification-link',
        description: 'Повторно отправляет ссылку для подтверждения email',
        summary: 'Повторная верификация email',
        security: [["sanctumAuth" => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ссылка успешно отправлена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Ссылка для верификации почтового адреса успешно отправлена')
                    ],
                    type: 'object')),
            new OA\Response(
                response: 400,
                description: 'Некорректная ссылка',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Некорректная ссылка')
                    ],
                    type: 'object')),
            new OA\Response(
                response: 410,
                description: 'Срок действия ссылки истёк',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Срок действия ссылки истёк')
                    ],
                    type: 'object')),
            new OA\Response(
                response: 409,
                description: 'Электронная почта уже подтверждена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Электронная почта уже подтверждена')
                    ],
                    type: 'object')),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Внутренняя ошибка сервера')
                    ],
                    type: 'object')
            )
        ]
    )]
    public function resendVerificationLink(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $this->emailVerificationService->initiateEmailVerificationResend(
                $user['id'],
                $user['name'],
                $user['email']);
        } catch (\Throwable $exception) {
            if ($exception instanceof InvalidVerificationException) {
                return new JsonResponse(['message' => 'Некорректный линк'], $exception->getCode());
            }
            if ($exception instanceof ExpiredVerificationException) {
                return new JsonResponse(['message' => 'Срок действия ссылки истёк'], $exception->getCode());
            }
            if ($exception instanceof EmailAlreadyVerifiedException) {
                return new JsonResponse(['message' => 'Электронная почта уже подтверждена'], $exception->getCode());
            }
            else {
                return new JsonResponse(['message' => 'Внутренняя ошибка сервера'], 500);
            }
        }

        return new JsonResponse(['message' => 'Ссылка для верификации почтового адреса успешно отправлена'], 200);
    }
}

