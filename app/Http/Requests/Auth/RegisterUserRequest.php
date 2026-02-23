<?php

namespace App\Http\Requests\Auth;

use App\Rules\RegixPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\BaseRequest;

class RegisterUserRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Summary of prepareForValidation
     * @return void
     */
    protected function prepareForValidation() {
        $this->merge([
            'name' => [
                'en' => $this->input('name.en', ''),
                'ar' => $this->input('name.ar', ''),
            ],
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [

            'name.en'           => ['required', 'string', 'max:125'],
            'name.ar'           => ['nullable', 'string', 'max:125'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols(), new RegixPassword()],
          
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array {
        return [
            'required'          => 'the :attribute is required',
            'email.required'    => 'the email address is required.',
            'email.email'       => 'the email must be a valid email address.',
            'email.unique'      => 'this email is already registered.',
            'password.required' => 'the password is required.',
            'password.confirmed' => 'the password confirmation does not match.',
            'password.min'      => 'the password must be at least :min characters.',
           
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array {
        return [
            'name.en'    => 'name (English)',
            'name.ar'    => 'name (Arabic)',
            'email'      => 'email address',
            'password'   => 'password',
            
        ];
    }
}
