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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'password' => ['nullable', 'min:6', 'same:password_confirmation'],
            'password_confirmation' => ['nullable'],
            'file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', "max:$maxFileSizeInMB"],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->somethingElseIsInvalid($validator)) {
                    $validator->errors()->add(
                        'password',
                        'Неверный пароль'
                    );
                }
            }
        ];
    }

    public function somethingElseIsInvalid(Validator $validator): bool
    {
        return !Auth::guard('api')->attempt([
            'email' => $validator->safe()->email,
            'password' => $validator->safe()->password,
        ]);
    }
}
