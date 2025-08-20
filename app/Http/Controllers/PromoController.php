<?php

namespace App\Http\Controllers;


use App\Http\Resources\FilmResource;
use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Attributes as OA;


class PromoController extends Controller
{
    function __construct(protected PromoService $promoService){ }

    #[OA\Get(
        path: '/api/promo',
        description: 'Маршрут предоставляет данные о промо-фильме.',
        summary: 'Получить данные промо-фильма',
        tags: ['Promo'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные о промо-фильме',
                content: new OA\JsonContent(ref: '#/components/schemas/FilmResource')),
            new OA\Response(
                response: 404,
                description: 'Промо-фильм не был предварительно установлен',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Промо-фильм не установлен')],
                    type: 'object'))
        ]
    )]
    public function getPromo(): FilmResource | JsonResponse
    {
        try {
            $resource = $this->promoService->getPromo();
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return $resource;
    }

    #[OA\Post(
        path: '/api/promo/{id}',
        description: 'Установить промо-фильм. Авторизация: доступен только пользователям с ролью "moderator"',
        summary: 'Установить промо-фильм',
        security: [["sanctumAuth" => []]],
        tags: ['Promo'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Идентификатор фильма', in: 'path', example: '010a26e3-b835-4611-bfe9-d7bba5324417'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of comments',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Промо-фильм установлен успешно')],
                        type: 'object')),
            new OA\Response(
                response: 404,
                description: 'Фильм с предоствленным id не существует',
                content: new OA\JsonContent(
                    properties: [new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Фильм с предоствленным id не существует')],
                        type: 'object'))
        ]
    )]
    public function setPromo(string $id): JsonResponse
    {
        try {
            $this->promoService->setPromo($id);
        } catch (\Exception $exception) {
            if($exception instanceof NotFoundHttpException) {
                return new JsonResponse(['message' => $exception->getMessage()], 404);
            }
            return new JsonResponse(['message' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Промо-фильм установлен успешно'], 200);
    }
}
