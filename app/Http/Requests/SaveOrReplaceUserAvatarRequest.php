<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SaveOrReplaceUserAvatarRequest',
    type: 'object'
)]
class SaveOrReplaceUserAvatarRequest extends FormRequest
{
    #[OA\Property(property: 'file', description: 'User avatar (png, jpg, jpeg, svg)', type: 'file', nullable: true)]
    public UploadedFile $file;
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
