<?php

namespace Modules\CourseManagement\Http\Requests\Api\V1\Lesson;

use App\Enums\LessonType;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\CourseManagement\Models\Lesson;
use Modules\CourseManagement\Rules\AtLeastOneLocale;

class UpdateLessonRequest extends BaseRequest {

    /**
     * Summary of rules
     * @return array{assignment_file: array<string|\Illuminate\Validation\Rules\RequiredIf>, assignment_file.*: string[], content: array<string|\Illuminate\Validation\Rules\RequiredIf>, file: array<string|\Illuminate\Validation\Rules\RequiredIf>, file.*: string[], live_url: array<string|\Illuminate\Validation\Rules\RequiredIf>, order: array<string|\Illuminate\Validation\Rules\Unique>, section_id: string[], title: array<AtLeastOneLocale|string>, title.ar: string[], title.en: string[], type: array<Enum|string>, videos: array<string|\Illuminate\Validation\Rules\RequiredIf>, videos.*: string[]}
     */
    public function rules(): array {
        $lesson = $this->route('lesson');
        $sectionId = $this->input('section_id', $lesson->section_id);

        return [
            'section_id' => ['bail', 'sometimes', 'integer', 'exists:sections,id'],
            'title' => ['sometimes', 'array', new AtLeastOneLocale()],
            'title.en' => ['sometimes', 'nullable', 'string', 'max:150', 'min:3'],
            'title.ar' => ['sometimes', 'nullable', 'string', 'max:150', 'min:3'],
            'content' => [
                'nullable',
                'string',
                'max:4096',
                Rule::requiredIf(fn() => $this->input('type') === LessonType::Artical->value)
            ],

            'type' => ['sometimes', new Enum(LessonType::class)],
            'order' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
                Rule::unique('lessons')
                    ->where(fn($query) => $query->where('section_id', $sectionId))
                    ->ignore($lesson->id),
            ],
            'videos' => [
                Rule::requiredIf(fn() => $this->input('type') === LessonType::Video->value),
                'array',
                'max:5'
            ],
            'videos.*' => [
                'file',
                'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska',
                'max:512000', 
            ],
            'file' => [
                Rule::requiredIf(fn() => $this->input('type') === LessonType::File->value),
                'array',
                'max:5'
            ],
            'file.*' => [
                'file',
                'max:102400' 
            ],
            'assignment_file' => [
                Rule::requiredIf(fn() => $this->input('type') === LessonType::Assignment->value),
                'array',
                'max:5'
            ],
            'assignment_file.*' => [
                'file',
                'max:102400'
            ],
      
        ];
    }

    /**
     * Summary of messages
     * @return array{assignment_file.*.file: string, assignment_file.*.max: string, assignment_file.array: string, assignment_file.required_if: string, file.*.file: string, file.*.max: string, file.array: string, file.required_if: string, live_url.required_if: string, live_url.url: string, videos.*.file: string, videos.*.max: string, videos.*.mimetypes: string, videos.array: string, videos.max: string, videos.required: string}
     */
    public function messages(): array {
        return [
            'videos.required' => 'At least one video is required when the lesson type is video.',
            'videos.array' => 'Videos must be sent as an array.',
            'videos.max' => 'You may upload a maximum of :max videos.',
            'videos.*.file' => 'Each video must be a valid file.',
            'videos.*.mimetypes' => 'Each video must be a valid video format (mp4, mov, avi, mkv).',
            'videos.*.max' => 'Each video may not be greater than :max kilobytes.',

            'file.required_if' => 'File is required for File lessons.',
            'file.array' => 'Files must be sent as an array.',
            'file.*.file' => 'Each file must be valid.',
            'file.*.max' => 'Each file may not exceed :max kilobytes.',

            'assignment_file.required_if' => 'Assignment file is required for Assignment lessons.',
            'assignment_file.array' => 'Assignment files must be sent as an array.',
            'assignment_file.*.file' => 'Each assignment must be a valid file.',
            'assignment_file.*.max' => 'Each assignment file may not exceed :max kilobytes.',

           
        ];
    }
    /**
     * Summary of attributes
     * @return array{assignment_file: string, assignment_file.*: string, file: string, file.*: string, live_url: string, videos: string, videos.*: string}
     */
    public function attributes(): array {
        return [
            'videos' => 'lesson videos',
            'videos.*' => 'lesson video',
            'file' => 'files',
            'file.*' => 'file',
            'assignment_file' => 'assignment files',
            'assignment_file.*' => 'assignment file',
            'live_url' => 'live URL',
        ];
    }

    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('lesson'));
    }
}