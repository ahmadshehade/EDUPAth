<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Categories;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Modules\CourseManagement\Models\Category;

class StoreCategoryRequest extends BaseRequest {
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void {
        $name = array_filter([
            'en' => $this->input('name.en'),
            'ar' => $this->input('name.ar')
        ], fn($value) => !is_null($value));

        $this->merge([
            'name' => $name,
            'slug' => Str::slug($name['en'] ?? $name['ar'] ?? 'default-slug')
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array {
        $rules = [];

        foreach (['en', 'ar'] as $locale) {
            $rules["name.$locale"] = [
                'required',
                'string',
                'min:4',
                'max:125',
                function ($attribute, $value, $fail) use ($locale) {
                    $exists = Category::whereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) = ?",
                        [$value]
                    )->exists();

                    if ($exists) {
                        $fail($locale === 'en'
                            ? 'The English name must be unique.'
                            : 'The Arabic name must be unique.');
                    }
                }
            ];
        }

        $rules['image'] = ['sometimes', 'image', 'mimes:jpeg,png,gif', 'max:4096'];
        $rules['parent_id'] = ['sometimes', 'integer', 'exists:categories,id'];
        $rules['slug'] = ['string', 'max:255'];

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array {
        return [
            'name.en.required' => 'The English name is required.',
            'name.en.string' => 'The English name must be a string.',
            'name.en.min' => 'The English name must be at least :min characters.',
            'name.en.max' => 'The English name must not exceed :max characters.',

            'name.ar.required' => 'The Arabic name is required.',
            'name.ar.string' => 'The Arabic name must be a string.',
            'name.ar.min' => 'The Arabic name must be at least :min characters.',
            'name.ar.max' => 'The Arabic name must not exceed :max characters.',

            'parent_id.integer' => 'The parent category ID must be an integer.',
            'parent_id.exists' => 'The selected parent category does not exist.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array {
        return [
            'name.en' => 'English name',
            'name.ar' => 'Arabic name',
            'parent_id' => 'parent category',
        ];
    }

    /**
     * Authorization check.
     */
    public function authorize(): bool {
        return Gate::allows('adminJob');
    }
}
