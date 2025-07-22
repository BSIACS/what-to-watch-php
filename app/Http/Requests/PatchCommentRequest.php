<?php

namespace App\Http\Requests;

use App\Models\Film;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PatchCommentRequest',
    properties: [
        new OA\Property(property: 'text', description: 'Comment content', type: 'string', example: 'Alice guessed who it was, even before she came upon a Gryphon, lying fast asleep in the act of crawling away: besides all this, there was no more of the cattle in the window, I only wish it was,\' he said. (Which he certainly did NOT, being made.', nullable: true),
    ],
    type: 'object',
)]
class PatchCommentRequest extends FormRequest
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
            'text' => ['required', 'min:5', 'max:400'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'text.min' => 'Мнимальное количество символов 5',
            'text.max' => 'Максимальное количество символов 400',
        ];
    }
}
