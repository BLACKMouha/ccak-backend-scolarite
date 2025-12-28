<?php

namespace Database\Factories;

use App\Models\AcademicProgram;
use App\Models\AcademicYear;
use App\Models\DeliberationSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliberationSessionFactory extends Factory
{
    protected $model = DeliberationSession::class;

    public function definition(): array
    {
        return [
            'academic_program_id' => AcademicProgram::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester' => $this->faker->numberBetween(1, 2),
            'session_name' => 'Jury ' . $this->faker->word() . ' S' . $this->faker->numberBetween(1, 2) . ' - Session Normale',
            'session_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(DeliberationSession::getStatuses()),
            'presided_by' => User::factory(),
            'jury_members' => null,
        ];
    }
}
