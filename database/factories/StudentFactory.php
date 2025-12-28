<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_number' => $this->faker->unique()->numerify('STD######'),
            'full_name' => $this->faker->name(),
        ];
    }
}
