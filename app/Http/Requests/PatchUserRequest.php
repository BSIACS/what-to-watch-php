<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class PatchUserRequest extends FormRequest
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
            'file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', "max:$maxFileSizeInMB"],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Допустимы только символы А-Я а-я A-Z a-z . _',
            'email' => 'Поле должно содержать валидный email адрес',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'file.mimes' => 'Допустимы только png, jpeg и svg расширения',
            'file.image' => 'Файл должен быть изображением',
            'file.max' => 'Максимальный размер загружаемого файла 10MB',
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
