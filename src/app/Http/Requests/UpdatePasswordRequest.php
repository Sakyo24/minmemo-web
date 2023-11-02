<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AlphaNumRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', 'between:8,16', new AlphaNumRule()],
            'password_confirmation' => ['required'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'password' => 'パスワード',
            'password_confirmation' => '確認用パスワード',
        ];
    }
}
