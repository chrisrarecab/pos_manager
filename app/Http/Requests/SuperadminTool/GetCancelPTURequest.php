<?php

namespace App\Http\Requests\SuperadminTool;

use Illuminate\Foundation\Http\FormRequest;

class GetCancelPTURequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clientgroupid' => 'required|integer',
            'networkid'     => 'required|integer',
            'branchid'      => 'required|integer',
            'terminalno'    => 'required|integer'
        ];
    }
}