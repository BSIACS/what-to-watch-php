<?php

namespace App\Http\Requests;

use App\Models\Film;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PatchFilmRequest extends FormRequest
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
        $maxFileSizeInMB = (string)(1024 * 10);
        $maxVideoSizeInMB = (string)(1024 * 100);
        $maxPreviewVideoSizeInMB = (string)(1024 * 50);


        return [
            'name' => ['string', 'max:255'],
            'posterImage' => ['mimes:jpg,jpeg', 'image', "max:$maxFileSizeInMB"],
            'previewImage' => ['mimes:jpg,jpeg', 'image', "max:$maxFileSizeInMB"],
            'backgroundImage' => ['mimes:jpg,jpeg', 'image', "max:$maxPreviewVideoSizeInMB"],
            'backgroundColor' => ['string', 'max:255'],
            'released' => ['integer', 'min_digits:4, max_digits:4'],
            'description' => ['string', 'max:1000'],
            'starring' => ['array', 'max:20'],
            'starring.*' => ["regex:/^[a-zA-Zа-яА-Я.' ]{1,50}$/u"],
            'runtime' => ['integer', 'max:9999'],
            'video' => ['mimes:mp4', "max:$maxVideoSizeInMB"],
            'previewVideo' => ['mimes:mp4', "max:$maxPreviewVideoSizeInMB"],
            'status_id' => ['exists:film_statuses,id'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'video' => 'Загружаемый файл должен быть видео файлом',
            'mimes' => 'Допустим только mp4 формат файла',
        ];
    }
}
