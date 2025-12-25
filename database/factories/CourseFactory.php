<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_unit_id' => CourseUnit::factory(),
            'code' => strtoupper(fake()->unique()->bothify('EC###')),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'credits' => fake()->numberBetween(1, 15),
            'hours_lecture' => fake()->numberBetween(0, 60),
            'hours_td' => fake()->numberBetween(0, 60),
            'hours_tp' => fake()->numberBetween(0, 60),
            'coefficient' => fake()->randomFloat(2, 0.5, 5),
            'prerequisites' => [],
            'is_active' => true,
        ];
    }
}
