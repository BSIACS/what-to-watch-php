<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateGenreRequest',
    type: 'object'
)]
class UpdateGenreRequest extends FormRequest
{
    #[OA\Property(property: 'name', description: 'Название жанра', type: 'string', example: 'Action', nullable: false)]
    public string $name;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
        ];
    }
}
