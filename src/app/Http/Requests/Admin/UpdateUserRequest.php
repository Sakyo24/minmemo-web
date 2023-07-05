<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        // dd($this->input('email'));
        return [
            'name' => ['required', 'max:25'],
            'email' => ['required', 'max:255', 'email:filter', Rule::unique('users')->ignore($this->input('id'))],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => '名前',
            'email' => 'メールアドレス',
        ];
    }
}
