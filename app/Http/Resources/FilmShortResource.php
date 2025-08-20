<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "FilmShortResource",
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name'),
        new OA\Property(property: 'preview_image'),
        new OA\Property(property: 'preview_video_link')
    ]
)]
class FilmShortResource extends JsonResource
{
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
            'preview_image' => $this->preview_image,
            'preview_video_link' => $this->preview_video_link,
        ];
    }
}
