<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Gate;

class StoreProfileRequest extends BaseRequest {

    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool {
        return Gate::allows('create', Profile::class);
    }

    /**
     * Summary of prepareForValidation
     * @return void
     */
    protected function prepareForValidation() {

        $data = [];
        if ($this->has('address')) {
            $data['address'] = array_filter(
                [
                    'en' => $this->input('address.en'),
                    'ar' => $this->input('address.ar')
                ],
                fn($value) => !is_null($value)
            );
        }
        if ($this->has('gender')) {
            $data['gender'] = array_filter(
                [
                    'ar' => $this->input('gender.ar'),
                    'en' => $this->input('gender.en')
                ],
                fn($value) => !is_null($value)
            );
        }
        $this->merge($data);
    }

    /**
     * Summary of rules
     * @return array{address.ar: string[], address.en: string[], bio: string[], date_of_birth: string[], gender.ar: string[], gender.en: string[], phone: string[], social_links: string[], website: string[]}
     */
    public function rules(): array {
        return [
            'phone'          => ['sometimes', 'string', 'min:8', 'max:32', 'unique:profiles,phone'],
            'bio'            => ['sometimes', 'string', 'min:5', 'max:255'],
            'address.en'     => ['sometimes', 'string', 'min:3', 'max:125'],
            'address.ar'     => ['sometimes', 'string', 'min:3', 'max:125'],
            'date_of_birth'  => ['sometimes', 'date'],
            'gender.en'      => ['sometimes', 'string', 'in:male,female'],
            'gender.ar'      => ['sometimes', 'string', 'in:ذكر,أنثى'],
            'social_links'   => ['sometimes', 'json'],
            'website'        => ['sometimes', 'string', 'min:5', 'max:255'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,gif', 'max:4096']
        ];
    }

    /**
     * Summary of messages
     * @return array{date_of_birth.date: string, gender.ar.in: string, gender.en.in: string, phone.unique: string, social_links.json: string}
     */
    public function messages(): array {
        return [
            'phone.unique'      => 'The phone number has already been taken.',
            'date_of_birth.date' => 'The date of birth is not a valid date.',
            'gender.en.in'      => 'The selected gender (English) is invalid. Allowed values: male, female.',
            'gender.ar.in'      => 'The selected gender (Arabic) is invalid. Allowed values: ذكر, أنثى.',
            'social_links.json' => 'The social links must be a valid JSON string.',
            'image.image'       => 'The file must be an image.',
            'image.mimes'       => 'The image must be a file of type: jpeg, png, gif.',
            'image.max'         => 'The image may not be greater than 4096 kilobytes.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{address.ar: string, address.en: string, bio: string, date_of_birth: string, gender.ar: string, gender.en: string, phone: string, social_links: string, website: string}
     */
    public function attributes(): array {
        return [
            'phone'          => 'phone number',
            'bio'            => 'biography',
            'address.en'     => 'address (English)',
            'address.ar'     => 'address (Arabic)',
            'date_of_birth'  => 'date of birth',
            'gender.en'      => 'gender (English)',
            'gender.ar'      => 'gender (Arabic)',
            'social_links'   => 'social links',
            'website'        => 'website',
            'image' => 'image'
        ];
    }
}
