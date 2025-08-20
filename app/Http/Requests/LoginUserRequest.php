<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginUserRequest',
    type: 'object'
)]
class LoginUserRequest extends FormRequest
{
    #[OA\Property(property: 'email', description: 'User email', type: 'string', example: 'ivan@somemail.ru', nullable: false)]
    public string $email;

    #[OA\Property(property: 'password', description: 'Password', type: 'string', example: 'ud5R#gh78Qi!6', nullable: false)]
    public string $password;
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
            'email' => ['required'],
            'password' => ['required'],
        ];
    }
}
