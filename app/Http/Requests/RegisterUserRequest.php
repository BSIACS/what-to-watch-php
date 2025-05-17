<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
    public function rules(): array
    {
        $maxFileSizeInMB = (string)(1024 * 10);

        return [
            'name' => ['required', 'regex:/^[\p{L}\p{N}_.]{3,16}$/u', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['same:password'],
            'file' => ['mimes:png,jpg,jpeg,svg', 'image', "max:$maxFileSizeInMB"],
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
            'file.mimes' => 'Допустимы только png, jpeg и svg расширения',
            'file.image' => 'Файл должен быть изображением',
            'file.max' => 'Максимальный размер загружаемого файла 10MB',
        ];
    }
}
