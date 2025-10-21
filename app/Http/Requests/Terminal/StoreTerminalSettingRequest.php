<?php

namespace App\Http\Requests\Terminal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StoreTerminalSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'isSuccessful' => false,
            'values' => null,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => 'required|uuid',

            'settings' => 'nullable|array',
            'settings.*.name' => 'required_with:settings|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'nullable',

            'terminalConnections' => 'nullable|array'
        ];
    }
}
