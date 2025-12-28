<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'student_number' => 'STU' . fake()->unique()->numberBetween(1000, 9999),
            'full_name' => fake()->name(),
        ];
    }

    public function withUser(): static
    {
        return $this->afterCreating(function (Student $student) {
            $user = User::factory()->create([
                'email' => fake()->unique()->safeEmail(),
                'user_type' => 'STUDENT',
                'is_active' => true,
            ]);

            $student->update(['user_id' => $user->id]);
        });
    }
}
