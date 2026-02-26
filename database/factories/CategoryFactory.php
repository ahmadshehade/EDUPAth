<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\CourseManagement\Models\Category;


/**
 * Summary of CategoryFactory
 */
class CategoryFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Category::class;
    public function definition(): array {
        
        return [
            'name' => [
                'en'=>$this->faker->unique()->name(),
                'ar'=>$this->faker->unique()->name()
            ],
            'slug' => $this->faker->slug() ,
            'parent_id' => null
        ];
    }
}
