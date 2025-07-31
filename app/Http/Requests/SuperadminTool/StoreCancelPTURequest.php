<?php

namespace App\Http\Requests\SuperadminTool;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreCancelPTURequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clientgroupid'     => 'required|integer',
            'networkid'         => 'required|integer',
            'branchid'          => 'required|integer',
            'terminalno'        => 'required|integer',
            'reference_number'  => 'required'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400));
    }
}