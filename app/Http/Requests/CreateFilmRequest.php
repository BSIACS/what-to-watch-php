<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateFilmRequest',
    properties: [
        new OA\Property(property: 'imdbId', description: 'IMDb ID', type: 'string', example: 'tt0120102'),
    ],
    type: 'object',
)]
class CreateFilmRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array|string>
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'imdbId' => ['required', "regex:/^tt\d{7}$/"],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'imdbId.regex' => 'Поле должно соответствовать сигнатуре imdb идентификатора'
        ];
    }
}
