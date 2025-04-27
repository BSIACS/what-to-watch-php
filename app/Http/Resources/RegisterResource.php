<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
{
    protected string $userToken;

    function __construct($resource, $userToken)
    {
        parent::__construct($resource);
        $this->userToken = $userToken;
    }
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
            'token' => $this->userToken,
        ];
    }
}
