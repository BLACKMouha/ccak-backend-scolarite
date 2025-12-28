<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_number' => $this->faker->unique()->numerify('STU#######'),
            'full_name' => $this->faker->name(),
        ];
    }
}
