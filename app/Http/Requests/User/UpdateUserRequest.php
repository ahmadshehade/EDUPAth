<?php

namespace App\Http\Requests\User;

use App\Rules\RegixPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('user'));
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'name' => [
                'ar' => $this->input('name.ar'),
                'en' => $this->input('name.en'), // تم التصحيح
            ]
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name.en'           => ['sometimes', 'string', 'max:125'],
            'name.ar'           => ['sometimes', 'string', 'max:125'],
            'email'             => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'          => ['sometimes', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols(), new RegixPassword()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.en.string'    => 'The name (English) must be a string.',
            'name.ar.string'    => 'The name (Arabic) must be a string.',
            'name.en.max'       => 'The name (English) must not exceed 125 characters.',
            'name.ar.max'       => 'The name (Arabic) must not exceed 125 characters.',
            'email.email'       => 'The email must be a valid email address.',
            'email.unique'      => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min'      => 'The password must be at least :min characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name.en'    => 'name (English)',
            'name.ar'    => 'name (Arabic)',
            'email'      => 'email address',
            'password'   => 'password',
        ];
    }
}