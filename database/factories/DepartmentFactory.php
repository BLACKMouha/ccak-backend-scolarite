<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'name' => fake()->unique()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('DEP###')),
            'head_id' => null,
            'is_active' => true,
        ];
    }
}
