<?php

namespace App\Services;


use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoService
{
    public function getPromo(): FilmResource
    {
        $promo = Promo::with('film.genres')->first();

        if (!$promo || !$promo->film) {
            throw new NotFoundHttpException('Промо-фильм не установлен');
        }

        $genres = $promo->film->genres->pluck('name')->toArray();

        return new FilmResource($promo->film, $genres);
    }

    public function setPromo(string $id): void
    {
        $film = Film::query()->find($id);
        if($film === null) {
            throw new NotFoundHttpException('Фильм с предоствленным id не существует');
        }

        DB::beginTransaction();

        Promo::query()->first()?->delete();
        Promo::query()->create(['film_id' => $id]);

        DB::commit();
    }
}
