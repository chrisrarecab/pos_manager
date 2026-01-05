<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'secretKey' => 'required|string',
            'username' => 'required|string|alpha_dash|min:3|max:50',
            'password' => 'required|string|min:6',
            'fullname' => 'required|string|min:6|max:50',
        ];
    }
}
