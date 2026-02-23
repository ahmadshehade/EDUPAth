<?php

namespace App\Http\Requests\Auth;

use App\Rules\RegixPassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class LoginUserRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'email' => ['required', 'exists:users,email', 'email'],
            'password' => ['required', 'min:8', 'max:22', new RegixPassword()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array {
        return [
            'required' => 'the :attribute is required',
            'email'    => 'the :attribute must be a valid email address',
            'exists'   => 'the :attribute does not exist in our records',
            'min'      => 'the :attribute must be at least :min characters',
            'max'      => 'the :attribute may not be greater than :max characters',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array {
        return [
            'email'    => 'User Email',
            'password' => 'User Password',
        ];
    }
}
