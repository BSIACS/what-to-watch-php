<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


class GetFilmsRequest extends FormRequest
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

        return [
            'page' => ['integer'],
            'genre' => ['exists:genres,name'],
            'status' => [Rule::in(['ready','pending','on_moderation'])],
            'order_by' => [Rule::in(['released','rating'])],
            'order_to' => [Rule::in(['asc','desc'])],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->getValue('status') === 'pending' || $validator->getValue('status') === 'moderate') {
                    if (Auth::user()->role->name !== 'admin' && Auth::user()->role->name !== 'moderator'){
                        $validator->errors()->add(
                            'status',
                            'Значение выбранного фильтра не доступно для текущей роли пользователя'
                        );
                    }
                }
            }
        ];
    }

    public function messages()
    {
        return [
            // Пагинация
            'page.integer' => 'Номер страницы должен быть целым числом',

            // Жанр
            'genre.exists' => 'Указанный жанр не существует в базе данных',

            // Статус
            'status.in' => 'Неверный статус. Допустимые значения: ready, pending, on_moderation',

            // Сортировка по полю
            'order_by.in' => 'Неверное поле для сортировки. Допустимые значения: released, rating',

            // Направление сортировки
            'order_to.in' => 'Неверное направление сортировки. Допустимые значения: asc (по возрастанию), desc (по убыванию)'
        ];
    }
}
