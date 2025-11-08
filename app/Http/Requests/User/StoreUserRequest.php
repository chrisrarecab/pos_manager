<?php 

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'domain'   => 'required|string|max:255',
            'userId'   => 'required|string|max:100',
            'username' => 'required|string|alpha_dash|min:3|max:50',
            'fullname' => 'required|string|max:100',
            'password' => 'required|string|min:6',
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
