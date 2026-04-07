<?php

namespace Modules\Payments\Http\Requests\Api\V1\PaymentMethods;


use Illuminate\Validation\Rule;
use App\Enums\PaymentMethodType;
use App\Http\Requests\BaseRequest;

class StorePaymentMethodRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],

            'description' => ['nullable', 'string', 'max:255', 'min:5'],

            'code' => [
                'required',
                'string',
                'max:100',
                'unique:payment_methods,code'
            ],

            'type' => [
                'required',
                Rule::in(array_column(PaymentMethodType::cases(), 'value')),
            ],

            'is_active' => ['sometimes', 'boolean'],

            'config' => ['nullable', 'array'],
        ];
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->is_active ?? true,
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('adminJob');
    }

    /**
     * Summary of messages
     * @return array{code.unique: string, name.required: string, type.in: string}
     */
    public function messages(): array
    {
        return [

            'name.required' => 'The :attribute is required.',
            'name.string'   => 'The :attribute must be a valid string.',
            'name.min'      => 'The :attribute must be at least :min characters.',
            'name.max'      => 'The :attribute may not exceed :max characters.',

            'description.string' => 'The :attribute must be a valid string.',
            'description.min'    => 'The :attribute must be at least :min characters.',
            'description.max'    => 'The :attribute may not exceed :max characters.',

            'code.required' => 'The :attribute is required.',
            'code.string'   => 'The :attribute must be a valid string.',
            'code.max'      => 'The :attribute may not exceed :max characters.',
            'code.unique'   => 'This :attribute is already taken. Please use another one.',

            'type.required' => 'The :attribute is required.',
            'type.in'       => 'The selected :attribute is invalid.',

            'is_active.boolean' => 'The :attribute field must be true or false.',

            'config.array' => 'The :attribute must be a valid JSON object.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{code: string, config: string, description: string, is_active: string, name: string, type: string}
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
