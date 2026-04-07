<?php

namespace Modules\Payments\Http\Requests\Api\V1\PaymentMethods;


use Illuminate\Validation\Rule;
use App\Enums\PaymentMethodType;
use App\Http\Requests\BaseRequest;

class UpdatePaymentMethodRequest extends  BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $paymentMethodId = $this->route('paymentMethod')?->id ?? $this->route('paymentMethod');

        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],

            'description' => ['sometimes', 'nullable', 'string', 'min:5', 'max:255'],

            'code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('payment_methods', 'code')->ignore($paymentMethodId),
            ],

            'type' => [
                'sometimes',
                Rule::in(array_column(PaymentMethodType::cases(), 'value')),
            ],

            'is_active' => ['sometimes', 'boolean'],

            'config' => ['sometimes', 'nullable', 'array'],
        ];
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    /**
     * Authorization
     */
    public function authorize(): bool
    {
        return $this->user()->can('adminJob');
    }

    /**
     * Messages
     */
    public function messages(): array
    {
        return [

            'name.string' => 'The :attribute must be a valid string.',
            'name.min'    => 'The :attribute must be at least :min characters.',
            'name.max'    => 'The :attribute may not exceed :max characters.',


            'description.string' => 'The :attribute must be a valid string.',
            'description.min'    => 'The :attribute must be at least :min characters.',
            'description.max'    => 'The :attribute may not exceed :max characters.',


            'code.string' => 'The :attribute must be a valid string.',
            'code.max'    => 'The :attribute may not exceed :max characters.',
            'code.unique' => 'This :attribute is already taken.',


            'type.in' => 'The selected :attribute is invalid.',


            'is_active.boolean' => 'The :attribute field must be true or false.',


            'config.array' => 'The :attribute must be a valid JSON object.',
        ];
    }

    /**
     * Attributes
     */
    public function attributes(): array
    {
        return [
            'name' => 'payment method name',
            'description' => 'payment method description',
            'code' => 'payment method code',
            'type' => 'payment method type',
            'is_active' => 'status',
            'config' => 'configuration',
        ];
    }
}
