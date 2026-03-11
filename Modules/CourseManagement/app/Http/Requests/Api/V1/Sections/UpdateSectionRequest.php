<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Sections;

use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Modules\CourseManagement\Models\Course;
use Modules\CourseManagement\Models\Section;
use Modules\CourseManagement\Rules\AtLeastOneLocale;

class UpdateSectionRequest extends BaseRequest {
    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void {
        $titleEn = $this->input('title.en');
        $titleAr = $this->input('title.ar');

        $baseTitle = $titleEn ?: $titleAr;

        if ($baseTitle) {
            $this->merge([
                'slug' => Str::uuid() . '_' . Str::slug($baseTitle)
            ]);
        }
    }

    /**
     * Validation rules.
     */
    public function rules(): array {
        $section = $this->route('section');
        $user = Auth::user();
        $courseId = $this->input('course_id', $section->course_id);

        $rules = [
            'course_id' => ['sometimes', 'integer', 'exists:courses,id'],

            'title' => ['sometimes', 'nullable', 'array', new AtLeastOneLocale()],
            'title.en' => ['sometimes', 'nullable', 'string', 'min:4', 'max:125'],
            'title.ar' => ['sometimes', 'nullable', 'string', 'min:4', 'max:125'],

            'description' => ['sometimes', 'nullable', 'array', new AtLeastOneLocale()],
            'description.en' => ['sometimes', 'nullable', 'string', 'min:10', 'max:250'],
            'description.ar' => ['sometimes', 'nullable', 'string', 'min:10', 'max:250'],

            'order' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
                Rule::unique('sections', 'order')
                    ->where(fn($q) => $q->where('course_id', $courseId)->whereNull('deleted_at'))
                    ->ignore($section->id),
            ],

            'slug' => ['sometimes', 'nullable', 'string', Rule::unique('sections')->ignore($section->id)],

        ];

        $course = Course::find($courseId) ?? $section->course;
        if (
            $user->hasRole(UserRoles::Admin->value) ||
            ($user->hasRole(UserRoles::Instructor->value)
                && $course->is_published == true && $course->instructor->id === $user->id)
        ) {
            $rules['is_published'] = ['sometimes', 'boolean'];
        }

        return $rules;
    }

    /**
     * Authorization check.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('section'));
    }

    /**
     * Custom messages.
     */
    public function messages(): array {
        return [
            'course_id.exists' => 'Selected course does not exist.',
            'title.array' => 'Title must be a valid object.',
            'title.en.min' => 'English title must be at least :min characters.',
            'title.ar.min' => 'Arabic title must be at least :min characters.',
            'description.array' => 'Description must be a valid object.',
            'description.en.min' => 'English description must be at least :min characters.',
            'description.ar.min' => 'Arabic description must be at least :min characters.',
            'order.unique' => 'This order number is already used in this course.',
            'order.max' => 'Order cannot exceed :max.',
            'slug.unique' => 'Generated slug already exists. Please try again.',
            'is_published.boolean' => 'Publish status must be true or false.',
        ];
    }

    /**
     * Attribute names.
     */
    public function attributes(): array {
        return [
            'course_id' => 'course',
            'title' => 'title',
            'title.en' => 'English title',
            'title.ar' => 'Arabic title',
            'description' => 'description',
            'description.en' => 'English description',
            'description.ar' => 'Arabic description',
            'order' => 'section order',
            'slug' => 'slug',
            'is_published' => 'publish status',
        ];
    }
}
