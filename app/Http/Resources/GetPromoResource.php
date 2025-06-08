<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetPromoResource extends JsonResource
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
