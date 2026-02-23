<?php

namespace App\Http\Requests\RolesAndPermissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Gate::allows('adminJob');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $role = $this->route('role');
        return [
            'name' => ['required', 'string', 'min:3', 'max:18', Rule::unique('roles')->ignore($role->id)],
        ];
    }

    /**
     * Summary of messages
     * @return array{name.max: string, name.min: string, name.required: string, name.string: string, name.unique: string}
     */
    public function messages(): array {
        return [
            'name.required' => 'The role name field is required.',
            'name.string'   => 'The role name must be a string.',
            'name.min'      => 'The role name must be at least 3 characters.',
            'name.max'      => 'The role name must not exceed 18 characters.',
            'name.unique'   => 'This role name is already taken. Please choose another.',
        ];
    }

    /**
     * Get custom friendly attribute names for validation errors.
     */
    public function attributes(): array {
        return [
            'name' => 'role name',
        ];
    }
}
