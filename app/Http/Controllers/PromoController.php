<?php

namespace App\Http\Controllers;


use App\Http\Resources\GetPromoResource;
use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PromoController extends Controller
{
    function __construct(protected PromoService $promoService){ }

    public function getPromo(): GetPromoResource | JsonResponse
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
