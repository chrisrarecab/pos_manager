<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        return [
            'domainName' => [
                'nullable',
                'regex:/^[a-z0-9-]+\.[a-z0-9-]+\.ph$/i',
            ],
            'username' => 'required|string|alpha_dash|max:50',
            'password' => 'required|string|min:6',
            'softwareId' => 'required|int|min:1',
        ];
    }
}
