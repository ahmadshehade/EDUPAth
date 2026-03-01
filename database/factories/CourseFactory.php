<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CourseManagement\Models\Category;
use App\Models\User;
use Modules\CourseManagement\Models\Course;

class CourseFactory extends Factory {

    protected $model = Course::class;
    public function definition(): array {
        return [
            "title" => [
                'en' => $this->faker->sentence(3),
                'ar' => $this->faker->sentence(3),
            ],
            "description" => [
                'ar' => $this->faker->text(50),
                'en' => $this->faker->text(50),
            ],
            "level" => [
                'ar' => $this->faker->word(),
                'en' => $this->faker->word(),
            ],
            "category_id" => Category::inRandomOrder()->first()?->id ?? 1, // fallback إذا لا يوجد
            "is_published" => $this->faker->boolean(),
            "duration_hours" => $this->faker->numberBetween(20, 190),
            "price" => $this->faker->randomFloat(2, 50, 2000),
            "slug" => $this->faker->slug(),
            "instructor_id" => User::inRandomOrder()->first()?->id ?? 1,
        ];
    }
}
