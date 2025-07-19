<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "FilmResource",
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '010a26e3-b835-4611-bfe9-d7bba5324416'),
        new OA\Property(property: 'name', type: 'string', example: 'American History X'),
        new OA\Property(property: 'poster_image', type: 'string', example: 'poster/wvOl9GlXqt0e3MuXDpNARfATfVUkSp8e.jpg'),
        new OA\Property(property: 'preview_image', type: 'string', example: 'preview/ZfDXNJiAqsKfHpIOsmNrByopDahtOsVn.jpg'),
        new OA\Property(property: 'background_image', type: 'string', example: 'background/dJK1sOIlbTCOMjPaY48uXW7h6V71Bun3.jpg'),
        new OA\Property(property: 'background_color', type: 'string'),
        new OA\Property(property: "genres", type: "array", items: new OA\Items(type: "string"), example: ['Drama', 'Action']),
        new OA\Property(property: 'released', type: 'integer', example: 1998),
        new OA\Property(property: 'description', type: 'string', example: 'Living a life marked by violence, neo-Nazi Derek finally goes to prison after killing two black youths. Upon his release, Derek vows to change; he hopes to prevent his brother, Danny, who idolizes Derek, from following in his foot...'),
        new OA\Property(property: 'director', type: 'string', example: 'Tony Kaye'),
        new OA\Property(property: "starring", type: "array", items: new OA\Items(type: "string"), example: ['Edward Norton', 'Edward Furlong', 'Beverly D\'Angelo']),
        new OA\Property(property: 'run_time', type: 'integer', example: 119),
        new OA\Property(property: 'video_link', type: 'string', example: 'video/ha02ATj7xcrlkKTePBiHG8FzHYqgkVIQ.mp4'),
        new OA\Property(property: 'preview_video_link', type: 'string', example: 'previewVideo/IdpzDUwjwYMsSmYb0b7CGo59pgwiLN02.mp4'),
        new OA\Property(property: 'rating', type: 'integer', example: 4.9),
    ]
)]
class FilmResource extends JsonResource
{
    protected array $genres;

    function __construct($resource, $genres)
    {
        parent::__construct($resource);
        $this->genres = $genres;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'poster_image' => $this->poster_image,
            'preview_image' => $this->preview_image,
            'background_image' => $this->background_image,
            'background_color' => $this->background_color,
            'genres' => $this->genres,
            'released' => $this->released,
            'description' => $this->description,
            'director' => $this->director,
            'starring' => explode(', ',$this->starring),
            'run_time' => $this->run_time,
            'video_link' => $this->video_link,
            'preview_video_link' => $this->preview_video_link,
            'rating' => $this->score_count !== 0 ? round($this->rating / $this->score_count, 1) : 0,
        ];
    }
}
