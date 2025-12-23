<?php

namespace Database\Factories;

use App\Models\AcademicProgram;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicProgram>
 */
class AcademicProgramFactory extends Factory
{
    protected $model = AcademicProgram::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->unique()->words(3, true),
            'level' => fake()->randomElement(AcademicProgram::LEVELS),
            'duration_semesters' => fake()->numberBetween(2, 12),
            'total_credits_required' => fake()->numberBetween(60, 240),
            'is_active' => true,
        ];
    }
}
