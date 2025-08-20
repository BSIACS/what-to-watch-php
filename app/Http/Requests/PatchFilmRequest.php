<?php

namespace App\Http\Requests;

use App\Constants\FilmValidationConstants;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PatchFilmRequest',
    type: 'object'
)]
class PatchFilmRequest extends FormRequest
{
    #[OA\Property(property: 'name', description: 'Film title', type: 'string', example: 'American History X', nullable: true)]
    public string $name;

    #[OA\Property(property: 'posterImage', description: 'Big poster', type: 'file', nullable: true)]
    public UploadedFile $posterImage;

    #[OA\Property(property: 'previewImage', description: 'Preview (small image)', type: 'file', nullable: true)]
    public UploadedFile $previewImage;

    #[OA\Property(property: 'backgroundImage', description: 'Background image', type: 'file', nullable: true)]
    public UploadedFile $backgroundImage;

    #[OA\Property(property: 'backgroundColor', description: 'Color in hexadecimal encoding (#FFFFFF)', type: 'string', example: '', nullable: true)]
    public string $backgroundColor;

    #[OA\Property(property: 'released', description: 'Year of release', type: 'integer', example: 1998, nullable: true)]
    public int $released;

    #[OA\Property(property: 'description', description: 'Description', type: 'string', example: '', nullable: true)]
    public string $description;

    #[OA\Property(property: 'starring[]', description: 'List of actors', type: 'array',
        items: new OA\Items(type: 'string', example: 'Иван Иванов',),
        collectionFormat: "multi", example: ['Edward Norton', 'Edward Furlong'], nullable: true)]
    public array $starring = [];

    #[OA\Property(property: 'runtime', description: 'Movie duration', type: 'integer', example: '', nullable: true)]
    public int $runtime;

    #[OA\Property(property: 'video', description: 'Video file', type: 'file', nullable: true)]
    public UploadedFile $video;

    #[OA\Property(property: 'previewVideo', description: 'Preview video file', type: 'file', nullable: true)]
    public UploadedFile $previewVideo;

    #[OA\Property(property: 'status', description: 'Status: ready, pending, on_moderation', type: 'string', enum: ['ready','pending','on_moderation'], example: '', nullable: true)]
    public string $status;
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
            'name' => ['string', 'max:' . FilmValidationConstants::MAX_NANE_LENGTH],
            'posterImage' => ['mimes:jpg,jpeg', 'image', 'max:' . FilmValidationConstants::MAX_POSTER_IMAGE_SIZE],
            'previewImage' => ['mimes:jpg,jpeg', 'image', 'max:' . FilmValidationConstants::MAX_PREVIEW_IMAGE_SIZE],
            'backgroundImage' => ['mimes:jpg,jpeg', 'image', 'max:' . FilmValidationConstants::MAX_BACKGROUND_IMAGE_SIZE],
            'backgroundColor' => ['hex_color'],
            'released' => ['integer', 'min_digits:4, max_digits:4'],
            'description' => ['string', 'max:' . FilmValidationConstants::MAX_DESCRIPTION_LENGTH],
            'starring' => ['array', 'max:' . FilmValidationConstants::MAX_STARRING_ARRAY_LENGTH],
            'starring.*' => ["regex:/^[a-zA-Zа-яА-Я.' ]{1,50}$/u"],
            'runtime' => ['integer', 'max:9999'],
            'video' => ['mimes:mp4', 'max:' . FilmValidationConstants::MAX_VIDEO_SIZE],
            'previewVideo' => ['mimes:mp4', 'max:' . FilmValidationConstants::MAX_PREVIEW_VIDEO_SIZE],
            'status' => ['exists:film_statuses,name'],
        ];
    }

    public function messages()
    {
        return [
            // Общие поля
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не должно превышать ' . FilmValidationConstants::MAX_NANE_LENGTH . ' символов',

            // Изображения
            'posterImage.mimes' => 'Формат постера должен быть JPG или JPEG',
            'posterImage.image' => 'Постер должен быть изображением',
            'posterImage.max' => 'Размер постера не должен превышать ' . FilmValidationConstants::MAX_POSTER_IMAGE_SIZE . ' МБ',

            'previewImage.mimes' => 'Формат превью изображения должен быть JPG или JPEG',
            'previewImage.image' => 'Превью должно быть изображением',
            'previewImage.max' => 'Размер превью изображения не должен превышать ' . FilmValidationConstants::MAX_PREVIEW_IMAGE_SIZE . ' МБ',

            'backgroundImage.mimes' => 'Формат фонового изображения должен быть JPG или JPEG',
            'backgroundImage.image' => 'Фон должен быть изображением',
            'backgroundImage.max' => 'Размер фонового изображения не должен превышать ' . FilmValidationConstants::MAX_BACKGROUND_IMAGE_SIZE . ' МБ',

            'backgroundColor.hex_color' => 'Должно содержать допустимое значение цвета в формате шестнадцатеричного кода',

            // Год выпуска
            'released.integer' => 'Год выпуска должен быть целым числом',
            'released.min_digits' => 'Год выпуска должен содержать 4 цифры',
            'released.max_digits' => 'Год выпуска должен содержать 4 цифры',

            // Описание
            'description.string' => 'Описание должно быть строкой',
            'description.max' => 'Описание не должно превышать ' . FilmValidationConstants::MAX_DESCRIPTION_LENGTH . ' символов',

            // Актеры
            'starring.array' => 'Список актеров должен быть массивом',
            'starring.max' => 'Количество актеров не должно превышать ' . FilmValidationConstants::MAX_STARRING_ARRAY_LENGTH,
            'starring.*.regex' => "Имя актера должно соответствовать выражению /^[a-zA-Zа-яА-Я.' ]{1,50}$/u",

            // Длительность
            'runtime.integer' => 'Длительность должна быть целым числом',
            'runtime.max' => 'Длительность не должна превышать ' . FilmValidationConstants::MAX_RUNTIME_VALUE . ' минут',

            // Видео
            'video.mimes' => 'Формат видео должен быть MP4',
            'video.max' => 'Размер видео не должен превышать ' . FilmValidationConstants::MAX_VIDEO_SIZE . ' МБ',

            'previewVideo.mimes' => 'Формат превью видео должен быть MP4',
            'previewVideo.max' => 'Размер превью видео не должен превышать ' . FilmValidationConstants::MAX_PREVIEW_VIDEO_SIZE . ' МБ',

            // Статус
            'status_id.exists' => 'Указанный статус не существует в базе данных'
        ];
    }
}
