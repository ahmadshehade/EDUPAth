<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Enrollment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\CourseManagement\Models\Enrollment;

class StoreEnrollmentRequest extends FormRequest {
    /**
     * Summary of rules
     * @return array{course_id: string[]}
     */
    public function rules(): array {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id', function ($attribute, $value, $fail) {
                $userId = $this->user()->id;
                $exist = Enrollment::where('user_id', $userId)
                    ->where('course_id', $value)
                    ->exists();
                if ($exist) {
                    $fail('You are already enrolled in this course.');
                }
            }],
        ];
    }

    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool {
        return $this->user()->can('create', Enrollment::class);
    }


    /**
     * Custom error messages
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'course_id.required' => 'The course field is required.',
            'course_id.integer'  => 'The course ID must be a valid number.',
            'course_id.exists'   => 'The selected course does not exist.',
        ];
    }


    /**
     * Summary of attributes
     * @return array{course_id: string}
     */
    public function attributes(): array {
        return [
            'course_id' => 'course',
        ];
    }
}
