<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [ 'sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email,' . $this->route('user')->id],
            'roles' => ['sometimes', 'required', 'array', 'min:1'],
            'roles.*' => ['sometimes', 'required', 'string', 'in:admin,user,staff'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'confirm_password' => ['sometimes', 'required', 'string', 'same:password']
        ];
    }
}
