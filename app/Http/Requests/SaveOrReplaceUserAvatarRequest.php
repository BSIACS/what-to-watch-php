<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class SaveOrReplaceUserAvatarRequest extends FormRequest
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
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg,svg', "max:$maxFileSizeInMB"],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно',
            'file.mimes' => 'Допустимы только png, jpeg и svg расширения',
            'file.image' => 'Файл должен быть изображением',
            'file.max' => 'Максимальный размер загружаемого файла 10MB',
        ];
    }
}
