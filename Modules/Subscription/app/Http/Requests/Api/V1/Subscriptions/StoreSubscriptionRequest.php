<?php

namespace Modules\Subscription\Http\Requests\Api\V1\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRoles;
use App\Http\Requests\BaseRequest;

use Illuminate\Validation\Rule;
use Modules\Subscription\Models\Subscription;

class StoreSubscriptionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        $data['plan_id'] = ['sometimes', 'integer', 'exists:subscription_plans,id'];

        if ($this->user()->hasRole(UserRoles::Admin->value)) {
            $data['status'] = [
                'sometimes',
                Rule::in(array_column(SubscriptionStatus::cases(), 'value'))
            ];
        }
        $data['payment_method_id'] = ['required', 'integer', 'exists:payment_methods,id'];

        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Subscription::class);
    }

    /**
     * Summary of messages
     * @return array{plan_id.exists: string, plan_id.integer: string, plan_id.required: string}
     */
    public function messages()
    {
        return [
            'plan_id.required' => 'The :attribute field is required.',
            'plan_id.integer'  => 'The :attribute must be an integer.',
            'plan_id.exists'   => 'The selected :attribute is invalid.',
            'status.in'        => 'The selected :attribute is invalid.',
            'payment_method_id.required' => 'The payment method field is required.',
            'payment_method_id.integer'  => 'The payment method ID must be a valid number.',
            'payment_method_id.exists'   => 'The selected payment method is invalid.',
        ];
    }
    /**
     * Summary of attributes
     * @return array{plan_id: string}
     */
    public  function attributes()
    {
        return [
            'plan_id' => 'Plan',
            'status' => 'Subscription Status',
            'payment_method_id' => 'Payment Method'
        ];
    }
}
