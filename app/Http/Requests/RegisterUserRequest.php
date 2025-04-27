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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users'],
            //'password' => ['min:6', 'required_with:password_confirmation', 'same:password_confirmation'],
            'password' => ['required', 'min:6', 'same:password_confirmation'],
            'password_confirmation' => ['required'],
            'file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', "max:$maxFileSizeInMB"],
        ];
    }
}
