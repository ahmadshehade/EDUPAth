<?php

namespace Database\Factories;

use App\Enums\PaymentMethodType;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payments\Models\PaymentMethod;

/**
 * @extends Factory<Model>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model=PaymentMethod::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Visa', 'MasterCard', 'PayPal', 'Cash']),
            'description' => $this->faker->sentence(),
            'code' => $this->faker->unique()->slug(),
            'type'=>$this->faker->randomElement([PaymentMethodType::Online->value,PaymentMethodType::Offline->value]),
            'is_active'=> $this->faker->randomElement([true,false]),
        ];
    }
}
