<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Enrollment;

use Illuminate\Foundation\Http\FormRequest;
use Modules\CourseManagement\Models\Enrollment;

class UpdateEnrollmentRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $enrollment = $this->route('enrollment');
        $data = [
            'course_id' => ['sometimes', 'integer', 'exists:courses,id', function ($attribute, $value, $fail) use ($enrollment) {
                $userId = $this->user()->id;
                $exist = Enrollment::where('user_id', $userId)
                    ->where('course_id', $value)
                    ->where('id', '<>', $enrollment->id)
                    ->exists();
                if ($exist) {
                    $fail('You are already enrolled in this course.');
                }
            }],
        ];

        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('enrollment'));
    }

    /**
     * Summary of messages
     * @return array{course_id.exists: string, course_id.integer: string}
     */
    public function messages(): array {
        return [
            'course_id.integer' => 'The course ID must be a number.',
            'course_id.exists'  => 'The selected course does not exist.',
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
