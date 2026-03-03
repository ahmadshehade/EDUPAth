<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Sections;

use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\CourseManagement\Models\Section;

class UpdateSectionRequest extends BaseRequest {


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $section = $this->route('section');
        $user = Auth::user();
        $courseId = $this->course_id ?? $section->course_id;
        $data = [
            'course_id' => ['sometimes', 'integer', 'exists:courses,id'],
            'title.en' => ['nullable', 'string', 'min:4', 'max:125'],
            'title.ar' => ['nullable', 'string', 'min:4', 'max:125'],
            'title' => ['sometimes', 'array'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['nullable', 'string', 'min:10', 'max:250'],
            'description.ar' => ['nullable', 'string', 'min:10', 'max:250'],
            'order' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
                Rule::unique('sections')
                    ->where(fn($q) => $q->where('course_id', $courseId))
                    ->ignore($section->id)
            ],
         

        ];
        if ($user->hasRole(UserRoles::Admin->value)) {
            $data['is_published'] = ['sometimes', 'boolean'];
        }
        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()
            ->can('update', $this->route('section'));
    }

    /**
     * Summary of messages
     * @return array{course_id.exists: string, course_id.required: string, description.ar.min: string, description.array: string, description.en.min: string, description.required: string, is_published.boolean: string, order.max: string, order.unique: string, slug.unique: string, title.ar.min: string, title.array: string, title.en.min: string, title.required: string}
     */
    public function messages(): array {
        return [

            'course_id.exists'   => 'Selected course does not exist.',
            'title.array'        => 'Title must be a valid object.',
            'title.en.min'       => 'English title must be at least :min characters.',
            'title.ar.min'       => 'Arabic title must be at least :min characters.',
            'description.array'    => 'Description must be a valid object.',
            'description.en.min'   => 'English description must be at least :min characters.',
            'description.ar.min'   => 'Arabic description must be at least :min characters.',
            'order.unique' => 'This order number is already used in this course.',
            'order.max'    => 'Order cannot exceed :max.',
            'is_published.boolean' => 'Publish status must be true or false.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{course_id: string, description: string, description.ar: string, description.en: string, is_published: string, order: string, slug: string, title: string, title.ar: string, title.en: string}
     */
    public function attributes(): array {
        return [
            'course_id'      => 'course',
            'title'          => 'title',
            'title.en'       => 'English title',
            'title.ar'       => 'Arabic title',
            'description'    => 'description',
            'description.en' => 'English description',
            'description.ar' => 'Arabic description',
            'order'          => 'section order',
            'slug'           => 'slug',
            'is_published'   => 'publish status',
        ];
    }
}
