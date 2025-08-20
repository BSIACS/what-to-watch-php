<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "AvatarPathResource",
    properties: [
        new OA\Property(property: 'avatarPath', type: 'string', example: 'ef7d1976-5cd5-4c99-9bdf-cbd2209f214e/avatar/KgoFL8KpEtajLXJ225JjcMBIbMlKbXVd.jpg')
    ]
)]
class AvatarPathResource extends JsonResource
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
            'avatarPath' => $this->resource,
        ];
    }

}
