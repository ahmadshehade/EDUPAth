<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Course;

use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends BaseRequest {


    /**
     * Summary of prepareForValidation
     * @return void
     */
    public function prepareForValidation(): void {
        $course = $this->route('course');

        if (! $course) {
            return;
        }
        if ($this->has('title.en')) {
            $oldTitleEn = $course->title['en'] ?? null;
            $newTitleEn = $this->input('title.en');
            if ($newTitleEn && $newTitleEn !== $oldTitleEn) {
                $this->merge([
                    'slug' => Str::random(8) . '-' . Str::slug($newTitleEn),
                ]);
            }
        } elseif ($this->has('title.ar')) {
            $oldTitleAr = $course->title['ar'] ?? null;
            $newTitleAr = $this->input('title.ar');
            if ($newTitleAr && $newTitleAr !== $oldTitleAr) {
                $this->merge([
                    'slug' => Str::random(8) . '-' . Str::slug($newTitleAr),
                ]);
            }
        }
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $course = $this->route('course');
        $data = [
            'title' => ['sometimes', 'array'],
            'title.en' => [
                'sometimes',
                'string',
                'min:2',
                'max:125',
                Rule::unique('courses', 'title->en')->ignore($course->id),
            ],

            'title.ar' => [
                'sometimes',
                'string',
                'min:2',
                'max:125',
                Rule::unique('courses', 'title->ar')->ignore($course->id),
            ],
            'slug' => ['sometimes', 'string', Rule::unique('courses', 'slug')->ignore($course->id)],
            'description' => ['sometimes', 'array'],
            'description.en' => ['sometimes', 'string', 'max:500', 'min:2'],
            'description.ar' => ['sometimes', 'string', 'max:500', 'min:2'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:99999.99'],
            'duration_hours' => ['sometimes', 'integer', 'min:1', 'max:200'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'mimes:png,jpg,jpeg,gif', 'max:4096'],
            'level' => ['array'],
            'level.en' => ['sometimes', 'string', 'min:2', 'max:50'],
            'level.ar' => ['sometimes', 'string', 'min:2', 'max:50'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
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
        return Gate::allows('update', $this->route('course'));
    }

    /**
     * Summary of messages
     * @return array{description.array: string, description.required: string, images.*.image: string, images.*.max: string, images.*.mimes: string, price.numeric: string, price.required: string, slug.unique: string, title.ar.required: string, title.array: string, title.en.required: string, title.required: string}
     */
    public function messages(): array {
        return [
            'title.array' => 'Title must be a valid translation array.',
            'description.array' => 'Description must be a valid translation array.',
            'price.numeric' => 'Price must be a valid number.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Allowed image types: png, jpg, jpeg, gif.',
            'images.*.max' => 'Each image must not exceed 4MB.',

            'level.array' => 'Level must be a valid translation array.',

            'level.en.string' => 'English level must be a valid string.',
            'level.en.min' => 'English level must be at least 2 characters.',
            'level.en.max' => 'English level must not exceed 50 characters.',


            'level.ar.string' => 'Arabic level must be a valid string.',
            'level.ar.min' => 'Arabic level must be at least 2 characters.',
            'level.ar.max' => 'Arabic level must not exceed 50 characters.'
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
