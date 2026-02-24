<?php

namespace App\Http\Requests\RolesAndPermissions;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePermissionRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Gate::allows('adminJob');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'min:3', 'max:18', 'unique:permissions,name'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array {
        return [
            'name.required' => 'The permission name field is required.',
            'name.string'   => 'The permission name must be a string.',
            'name.min'      => 'The permission name must be at least 3 characters.',
            'name.max'      => 'The permission name must not exceed 18 characters.',
            'name.unique'   => 'This permission name is already taken. Please choose another.',
        ];
    }

    /**
     * Get custom friendly attribute names for validation errors.
     */
    public function attributes(): array {
        return [
            'name' => 'permission name',
        ];
    }
}
