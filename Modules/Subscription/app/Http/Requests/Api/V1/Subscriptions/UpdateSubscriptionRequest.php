<?php

namespace Modules\Subscription\Http\Requests\Api\V1\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends BaseRequest {
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $data['plan_id'] = ['sometimes', 'integer', 'exists:subscription_plans,id'];
        if ($this->user()->hasRole(UserRoles::Admin->value)) {
            $data['status'] = [
                'sometimes',
                Rule::in(array_column(SubscriptionStatus::cases(), 'value'))
            ];
        }
        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('subscription'));
    }

    /**
     * Summary of messages
     * @return array{plan_id.exists: string, plan_id.integer: string, plan_id.required: string}
     */
    public function messages() {
        return [
            'plan_id.integer' => 'plan_id Must Be Integer .',
            'plan_id.exists' => 'plan_id Must Be Exists .',
            'status.in'        => 'The selected :attribute is invalid.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{plan_id: string}
     */
    public  function attributes() {
        return [
            'plan_id' => 'Plan',
            'status' => 'Subscription Status',
        ];
    }
}
