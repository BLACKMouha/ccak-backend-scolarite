<?php

namespace Database\Factories;

use App\Models\AcademicProgram;
use App\Models\CourseUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseUnit>
 */
class CourseUnitFactory extends Factory
{
    protected $model = CourseUnit::class;

    public function definition(): array
    {
        return [
            'academic_program_id' => AcademicProgram::factory(),
            'code' => strtoupper(fake()->unique()->bothify('UE###')),
            'name' => fake()->unique()->words(3, true),
            'semester_number' => fake()->numberBetween(1, 12),
            'credits' => fake()->numberBetween(1, 30),
            'type' => fake()->randomElement(CourseUnit::TYPES),
            'is_active' => true,
        ];
    }
}
