<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterUserRequest',
    type: 'object'
)]
class RegisterUserRequest extends FormRequest
{
    #[OA\Property(property: 'name', description: 'User name', type: 'string', example: 'Ivan', nullable: false)]
    public string $name;

    #[OA\Property(property: 'email', description: 'User email', type: 'string', example: 'ivan@somemail.ru', nullable: false)]
    public string $email;

    #[OA\Property(property: 'password', description: 'Password', type: 'string', example: 'ud5R#gh78Qi!6', nullable: false)]
    public string $password;

    #[OA\Property(property: 'password_confirmation', description: 'Password confirmation', type: 'string', example: 'ud5R#gh78Qi!6', nullable: false)]
    public string $password_confirmation;
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
    public function rules(): array
    {
//        $maxFileSizeInMB = (string)(1024 * 10);

        return [
            'name' => ['required', 'regex:/^[\p{L}\p{N}_.]{3,16}$/u', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['same:password'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'name.regex' => 'Допустимы только символы А-Я а-я A-Z a-z . _',
            'email' => 'Поле должно содержать валидный email адрес',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'password_confirmation.same' => 'Значение должно совпадать со значением поля пароль',
        ];
    }
}
