<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateCommentRequest',
    properties: [
        new OA\Property(property: 'text', description: 'Текстовое содержимое комментария', type: 'string', example: 'Alice guessed who it was, even before she came upon a Gryphon, lying fast asleep in the act of crawling away: besides all this, there was no more of the cattle in the window, I only wish it was,\' he said. (Which he certainly did NOT, being made.', nullable: true),
        new OA\Property(property: 'comment_id', description: 'Идентификатор комментария. Указывается, если комментарий ответ на другой комментарий.', type: 'string', example: '03df3ff9-3a46-45c7-8f7c-e8fc54d6d458'),
    ],
    type: 'object',
)]
class CreateCommentRequest extends FormRequest
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
            'comment_id' => ['uuid'],
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
