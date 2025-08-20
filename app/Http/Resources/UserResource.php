<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "UserResource",
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Ivan'),
        new OA\Property(property: 'email', type: 'string', example: 'ivan@somemail.com'),
        new OA\Property(property: 'role', type: 'string', example: 'User'),
        new OA\Property(property: 'avatarPath', type: 'string', example: 'ef7d1976-5cd5-4c99-9bdf-cbd2209f214e/avatar/KgoFL8KpEtajLXJ225JjcMBIbMlKbXVd.jpg')
    ]
)]
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->name,
            'avatarPath' => $this->avatar_path,
        ];
    }
}
