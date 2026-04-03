<?php

namespace Modules\Subscription\Http\Requests\Api\V1\SubscriptionPlans;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Modules\CourseManagement\Rules\AtLeastOneLocale;
use Modules\Subscription\Models\SubscriptionPlan;

class UpdateSubscriptionPlanRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', SubscriptionPlan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $rules = [
            'name' => ['sometimes', 'array', new AtLeastOneLocale()],
            'name.en' => ['nullable', 'string', 'max:125', 'min:2'],
            'name.ar' => ['nullable', 'string', 'max:125', 'min:2'],

            'description' => ['sometimes', 'array', new AtLeastOneLocale()],
            'description.en' => ['nullable', 'string', 'min:2', 'max:500'],
            'description.ar' => ['nullable', 'string', 'min:2', 'max:500'],

            'price' => ['sometimes', 'numeric', 'between:0,99999.99'],

            'interval' => ['sometimes', 'string', 'in:year,month,week,day'],

            'features' => ['nullable', 'array'],

            'is_active' => ['sometimes', 'boolean'],
            'course_ids' => ['sometimes', 'array'],
            ['course_ids.*'] => ['integer', 'exists:courses,id'],
        ];

        if ($this->has('interval')) {
            $interval = $this->input('interval');
            switch ($interval) {
                case 'year':
                    $rules['interval_count'] = ['required', 'integer', 'min:1', 'max:4'];
                    break;
                case 'month':
                    $rules['interval_count'] = ['required', 'integer', 'min:1', 'max:12'];
                    break;
                case 'week':
                    $rules['interval_count'] = ['required', 'integer', 'min:1', 'max:52'];
                    break;
                case 'day':
                    $rules['interval_count'] = ['required', 'integer', 'min:1', 'max:360'];
                    break;
                default:
                    $rules['interval_count'] = ['required', 'integer', 'min:1'];
            }
        } else {
            $rules['interval_count'] = ['sometimes', 'integer', 'min:1'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array {
        return [
            'name.sometimes' => __('validation.required', ['attribute' => __('subscription::validation.attributes.name')]),
            'name.array' => __('validation.array', ['attribute' => __('subscription::validation.attributes.name')]),
            'name.*.max' => __('validation.max.string', ['attribute' => __('subscription::validation.attributes.name_locale'), 'max' => 125]),
            'name.*.min' => __('validation.min.string', ['attribute' => __('subscription::validation.attributes.name_locale'), 'min' => 2]),

            'description.sometimes' => __('validation.required', ['attribute' => __('subscription::validation.attributes.description')]),
            'description.array' => __('validation.array', ['attribute' => __('subscription::validation.attributes.description')]),
            'description.*.max' => __('validation.max.string', ['attribute' => __('subscription::validation.attributes.description_locale'), 'max' => 500]),
            'description.*.min' => __('validation.min.string', ['attribute' => __('subscription::validation.attributes.description_locale'), 'min' => 2]),

            'price.sometimes' => __('validation.required', ['attribute' => __('subscription::validation.attributes.price')]),
            'price.numeric' => __('validation.numeric', ['attribute' => __('subscription::validation.attributes.price')]),
            'price.between' => __('validation.between.numeric', ['attribute' => __('subscription::validation.attributes.price'), 'min' => 0, 'max' => 99999.99]),

            'interval.sometimes' => __('validation.required', ['attribute' => __('subscription::validation.attributes.interval')]),
            'interval.in' => __('validation.in', ['attribute' => __('subscription::validation.attributes.interval')]),

            'interval_count.required' => __('validation.required', ['attribute' => __('subscription::validation.attributes.interval_count')]),
            'interval_count.integer' => __('validation.integer', ['attribute' => __('subscription::validation.attributes.interval_count')]),
            'interval_count.min' => __('validation.min.numeric', ['attribute' => __('subscription::validation.attributes.interval_count'), 'min' => 1]),
            'interval_count.max' => __('validation.max.numeric', ['attribute' => __('subscription::validation.attributes.interval_count'), 'max' => ':max']),

            'features.array' => __('validation.array', ['attribute' => __('subscription::validation.attributes.features')]),

            'is_active.boolean' => __('validation.boolean', ['attribute' => __('subscription::validation.attributes.is_active')]),
            'course_ids.array' => 'Course IDs must be an array.',
            'course_ids.*.integer' => 'Each Course ID must be an integer.',
            'course_ids.*.exists' => 'One or more selected Course are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array {
        return [
            'name' => __('subscription::validation.attributes.name'),
            'name.en' => __('subscription::validation.attributes.name_en'),
            'name.ar' => __('subscription::validation.attributes.name_ar'),
            'description' => __('subscription::validation.attributes.description'),
            'description.en' => __('subscription::validation.attributes.description_en'),
            'description.ar' => __('subscription::validation.attributes.description_ar'),
            'price' => __('subscription::validation.attributes.price'),
            'interval' => __('subscription::validation.attributes.interval'),
            'interval_count' => __('subscription::validation.attributes.interval_count'),
            'features' => __('subscription::validation.attributes.features'),
            'is_active' => __('subscription::validation.attributes.is_active'),
        ];
    }
}
