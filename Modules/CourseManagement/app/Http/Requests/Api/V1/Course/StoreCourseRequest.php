<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Course;

use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\CourseManagement\Models\Course;

class StoreCourseRequest extends BaseRequest {

    /**
     * Summary of prepareForValidation
     * @return void
     */
    public function prepareForValidation(): void {
        $title = array_filter([
            'ar' => $this->input('title.ar'),
            'en' => $this->input('title.en')
        ], fn($value) => !is_null($value));
        $description = array_filter([
            'ar' => $this->input('description.ar'),
            'en' => $this->input('description.en')
        ], fn($value) => !is_null($value));
        $level = array_filter([
            'ar' => $this->input('level.ar'),
            'en' => $this->input('level.en')
        ], fn($value) => !is_null($value));
        $baseTitle = $this->input('title.en') ?? $this->input('title.ar');
        $slug = Str::random(8) . '-' . Str::slug($baseTitle);
        $this->merge([
            'title' => $title,
            'description' => $description,
            'level' => $level,
            'slug' => $slug,
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $data = [
            'title' => ['required', 'array'],
            'title.en' => [
                'required',
                'string',
                'min:2',
                'max:125',
                Rule::unique('courses', 'title->en'),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title.ar' => [
                'required',
                'string',
                'min:2',
                'max:125',
                Rule::unique('courses', 'title->ar'),
            ],
            'slug' => ['required', 'string', 'unique:courses,slug'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string', 'max:500', 'min:2'],
            'description.ar' => ['required', 'string', 'max:500', 'min:2'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'duration_hours' => ['sometimes', 'integer', 'min:1', 'max:200'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'mimes:png,jpg,jpeg,gif', 'max:4096'],
            'level' => ['required', 'array'],
            'level.en' => ['required', 'string', 'min:2', 'max:50'],
            'level.ar' => ['required', 'string', 'min:2', 'max:50'],
        ];

        if (Auth::user()?->hasRole(UserRoles::Admin->value)) {
            $data['is_published'] = ['sometimes', 'boolean'];
        }

        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Gate::allows('create', Course::class);
    }

    /**
     * Summary of messages
     * @return array{description.array: string, description.required: string, images.*.image: string, images.*.max: string, images.*.mimes: string, price.numeric: string, price.required: string, slug.unique: string, title.ar.required: string, title.array: string, title.en.required: string, title.required: string}
     */
    public function messages(): array {
        return [

            'level.required' => 'Course level is required.',
            'level.array' => 'Level must be a valid translation array.',

            'level.en.required' => 'English level is required.',
            'level.en.string' => 'English level must be a valid string.',
            'level.en.min' => 'English level must be at least 2 characters.',
            'level.en.max' => 'English level must not exceed 50 characters.',

            'level.ar.required' => 'Arabic level is required.',
            'level.ar.string' => 'Arabic level must be a valid string.',
            'level.ar.min' => 'Arabic level must be at least 2 characters.',
            'level.ar.max' => 'Arabic level must not exceed 50 characters.',
            
            'title.required' => 'The course title is required.',
            'title.array' => 'Title must be a valid translation array.',

            'title.en.required' => 'English title is required.',
            'title.ar.required' => 'Arabic title is required.',

            'description.required' => 'Course description is required.',
            'description.array' => 'Description must be a valid translation array.',

            'price.required' => 'Course price is required.',
            'price.numeric' => 'Price must be a valid number.',

            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Allowed image types: png, jpg, jpeg, gif.',
            'images.*.max' => 'Each image must not exceed 4MB.',

            'slug.unique' => 'This slug already exists.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{description.ar: string, description.en: string, duration_hours: string, images: string, is_published: string, price: string, title.ar: string, title.en: string}
     */
    public function attributes(): array {
        return [
            'title.en' => 'English title',
            'title.ar' => 'Arabic title',
            'description.en' => 'English description',
            'description.ar' => 'Arabic description',
            'price' => 'course price',
            'duration_hours' => 'course duration',
            'images' => 'course images',
            'is_published' => 'publish status',
        ];
    }
}
