<?php

namespace App\Http\Requests\TerminalSettings;

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
            'message' => 'Validation failed',
            'error' => $validator->errors(),
            'code' => 422,
            'settings' => [],
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
            'clientGroupId' => 'required|integer',
            'clientNetworkId' => 'required|integer',
            'clientBranchId' => 'required|integer',
            'terminalNo' => 'required|integer',
            'posType' => 'required|integer',
            'clientId' => 'nullable|integer',
            'locationId' => 'nullable|integer',

            'settings' => 'nullable|array',
            'settings.*.name' => 'required_with:settings|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'nullable',

            'settings.*.options' => 'nullable|array',
            'settings.*.options.*.option_name' => 'required_with:settings|string',
            'settings.*.options.*.option_value' => 'required_with:settings|string',

        ];
    }
}
