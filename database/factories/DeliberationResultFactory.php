<?php

namespace Database\Factories;

use App\Models\DeliberationResult;
use App\Models\DeliberationSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliberationResultFactory extends Factory
{
    protected $model = DeliberationResult::class;

    public function definition(): array
    {
        $isWithHonors = $this->faker->boolean(30);

        return [
            'deliberation_session_id' => DeliberationSession::factory(),
            'student_id' => $this->faker->uuid, // Student::factory()
            'decision' => $this->faker->randomElement(DeliberationResult::getDecisions()),
            'jury_remarks' => $this->faker->paragraph(),
            'is_with_honors' => $isWithHonors,
            'honor_level' => $isWithHonors ? $this->faker->randomElement(DeliberationResult::getHonorLevels()) : null,
        ];
    }
}
