<?php

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'full_name' => $this->faker->name(),
            'relationship' => $this->faker->randomElement(['FATHER', 'MOTHER', 'GUARDIAN']),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'address' => $this->faker->address(),
            'occupation' => $this->faker->jobTitle(),
        ];
    }

    /**
     * Indicate that the guardian is a father.
     */
    public function father(): static
    {
        return $this->state(fn(array $attributes) => [
            'relationship' => 'FATHER',
        ]);
    }

    /**
     * Indicate that the guardian is a mother.
     */
    public function mother(): static
    {
        return $this->state(fn(array $attributes) => [
            'relationship' => 'MOTHER',
        ]);
    }

    /**
     * Indicate that the guardian is a guardian.
     */
    public function guardian(): static
    {
        return $this->state(fn(array $attributes) => [
            'relationship' => 'GUARDIAN',
        ]);
    }
}
