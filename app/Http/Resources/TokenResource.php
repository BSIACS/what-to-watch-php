<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "TokenResource",
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '0faa527e-deb5-4e92-84f5-b08355d88350|c5O42jaXgOtaSin7KvU9EhF73jXlcFuwFpwOfZE2b4c34717')
    ]
)]
class TokenResource extends JsonResource
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
            'token' => $this->resource,
        ];
    }

}
