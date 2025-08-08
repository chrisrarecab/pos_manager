<?php 

namespace App\Http\Requests;

class UserRequest extends BaseFormRequest
{
    protected function storeRules(): array
    {
        return [
            'domain' => 'required|string|max:255',
            'userid' => 'required|string|max:100',
            'username' => 'required|string|alpha_dash|max:50',
            'fullname' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'admin' => 'sometimes|boolean'
        ];
    }

    // protected function updateRules(): array
    // {
    //     return [
    //         'name' => 'sometimes|string|max:255',
    //         'email' => 'sometimes|email|unique:users,email,' . $this->route('user'),
    //         'password' => 'nullable|min:8|confirmed',
    //     ];
    // }
}
