<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "GenresResource",
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '010a26e3-b835-4611-bfe9-d7bba5324416'),
        new OA\Property(property: 'name', type: 'string', example: 'Action'),
    ]
)]
class GenresResource extends JsonResource
{
    function __construct($resource)
    {
        parent::__construct($resource);
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
        ];
    }
}
