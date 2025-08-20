<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "CommentResource",
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'text'),
        new OA\Property(property: 'created_at'),
        new OA\Property(property: 'name'),
        new OA\Property(property: 'commentId', description: 'Возвращает null, если комментарий относится к фильму или id, если является ответом на другой комментарий', type: 'string', nullable: true),
    ]
)]
class CommentResource extends JsonResource
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
            'text' => $this->text,
            'createdAt' => $this->created_at,
            'name' => $this->name === null ? 'Гость' : $this->name,
            'commentId' => $this->comment_id
        ];
    }
}
