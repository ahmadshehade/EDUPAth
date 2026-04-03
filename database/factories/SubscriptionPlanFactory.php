<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model=SubscriptionPlan::class;
    public function definition(): array {
        
        return [
            'name' => [
                'en' => $this->faker->name,
                'ar' => $this->faker->name
            ],
            'description' => [
                'en' => $this->faker->text,
                'ar' => $this->faker->text
            ],
            'price' => $this->faker->randomFloat(2, 0, 9999.99),
            'interval' => $this->faker->randomElement(['day', 'month', 'year']),
            'interval_count' => $this->faker->numberBetween(1, 12),
            'features' => [
                'time' => $this->faker->date,
            ],
            'is_active' => $this->faker->boolean
        ];
    }
}
