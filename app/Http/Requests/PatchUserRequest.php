<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PatchUserRequest',
    type: 'object'
)]
class PatchUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxFileSizeInMB = (string)(1024 * 10);

        return [
            'name' => ['string', 'regex:/^[\p{L}\p{N}_.]{3,16}$/u', 'max:255'],
            'email' => ['email', 'string', 'max:255'],
            'password' => ['min:6'],
            'password_confirmation' => [],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Допустимы только символы А-Я а-я A-Z a-z . _',
            'email' => 'Поле должно содержать валидный email адрес',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->getValue('password') != '' && !$this->isPasswordConfirmationCorrect($validator)) {
                    $validator->errors()->add(
                        'password_confirmation',
                        'Значение должно совпадать со значением поля пароль'
                    );
                }
            }
        ];
    }

    public function isPasswordConfirmationCorrect(Validator $validator): bool
    {
        return $validator->safe()->password === $validator->safe()->password_confirmation;
    }
}
