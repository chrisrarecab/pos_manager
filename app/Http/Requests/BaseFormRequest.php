<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    public function rules(): array
    {
        return match (true) {
            $this->isMethod('post') => $this->callIfExists('storeRules'),
            $this->isMethod('put'), $this->isMethod('patch') => $this->callIfExists('updateRules'),
            default => [],
        };
    }

    protected function callIfExists(string $method): array
    {
        return method_exists($this, $method) ? $this->{$method}() : [];
    }
}
