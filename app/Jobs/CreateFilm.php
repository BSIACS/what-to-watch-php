<?php

namespace App\Jobs;

use App\DTO\PatchFilmDTO;
use App\Services\FilmService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class CreateFilm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $imdbId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imdbId)
    {
        $this->imdbId = $imdbId;
    }

    /**
     * Execute the job.
     */
    public function handle(FilmService $filmService): void
    {
        $response = Http::get('https://www.omdbapi.com/?i=' . $this->imdbId . '&apikey=' . env('OMDB_API_KEY'));
        $film = $response->json();

        $dto = new PatchFilmDTO([
            'name' => $film['Title'],
            'released' => $film['Year'],
            'description' => $film['Plot'],
            'director' => $film['Director'],
            'starring' => explode(', ', $film['Actors']),
            'genres' => $film['Genre'],
            'runtime' => $this->convertRuntimeFromStringToInt($film['Runtime']),
        ]);

        $filmService->patchFilmByImdbId($dto, $this->imdbId);
    }

    private function convertRuntimeFromStringToInt($input): int
    {
        try {
            $cleaned = trim(str_replace('min', '', $input));
            if (!is_numeric($cleaned)) {
                throw new Exception('Неверный формат числа');
            }

            return (int)$cleaned;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Ошибка при преобразовании: ' . $e->getMessage());
        }
    }
}
