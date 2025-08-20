<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;


#[OA\Schema(
    title: 'PaginatedFilmShortCollection',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/FilmShortResource', type: 'object')
        ),
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'per_page', type: 'integer', example: 8),
        new OA\Property(property: 'total_pages', type: 'integer', example: 4)
    ]
)]
class PaginatedFilmShortCollection extends ResourceCollection
{
    public $collects = FilmShortResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
