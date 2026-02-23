<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'], 
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'the email address is required.',
            'email.email' => 'the email must be a valid email address.',
            'email.exists' => 'this email does not exist in our records.',
            'token.required' => 'the reset token is required.',
            'password.required' => 'the password is required.',
            'password.confirmed' => 'the password confirmation does not match.',
            'password.min' => 'the password must be at least :min characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'token' => 'reset token',
            'password' => 'password',
        ];
    }
}