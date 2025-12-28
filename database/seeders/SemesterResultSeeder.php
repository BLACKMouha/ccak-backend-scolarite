<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemesterResult;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Enums\DecisionType;

class SemesterResultSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $academicYears = AcademicYear::all();
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'ADMIN');
        })->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Skipping semester results.');
            return;
        }

        if ($academicYears->isEmpty()) {
            $this->command->warn('No academic years found. Skipping semester results.');
            return;
        }

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Skipping semester results.');
            return;
        }

        // Create semester results for each student
        $students->each(function ($student) use ($academicYears, $admins) {
            $academicYear = $academicYears->random();

            // Create results for 1-2 semesters
            $numberOfSemesters = rand(1, 2);

            for ($semester = 1; $semester <= $numberOfSemesters; $semester++) {
                $totalCreditsEnrolled = fake()->randomFloat(2, 25.00, 35.00);
                $semesterAverage = fake()->randomFloat(2, 10.00, 18.00);
                $semesterGpa = $this->convertToGpa($semesterAverage);

                // Determine credits earned and decision based on average
                $totalCreditsEarned = $semesterAverage >= 10.00
                    ? $totalCreditsEnrolled
                    : fake()->randomFloat(2, $totalCreditsEnrolled * 0.5, $totalCreditsEnrolled * 0.9);

                $decision = $this->determineDecision($semesterAverage, $totalCreditsEarned, $totalCreditsEnrolled);

                SemesterResult::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'semester' => $semester,
                    'total_credits_enrolled' => $totalCreditsEnrolled,
                    'total_credits_earned' => $totalCreditsEarned,
                    'semester_average' => $semesterAverage,
                    'semester_gpa' => $semesterGpa,
                    'decision' => $decision,
                    'calculated_by' => $admins->random()->id,
                    'calculated_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        });

        $this->command->info('Semester results created successfully.');
    }

    private function convertToGpa(float $average): float
    {
        // Convert 0-20 scale to 4.0 GPA scale
        return match (true) {
            $average >= 18.00 => 4.0,
            $average >= 16.00 => 3.7,
            $average >= 14.00 => 3.3,
            $average >= 12.00 => 3.0,
            $average >= 10.00 => 2.7,
            $average >= 8.00 => 2.0,
            default => 1.0,
        };
    }

    private function determineDecision(float $average, float $creditsEarned, float $creditsEnrolled): DecisionType
    {
        $creditPercentage = ($creditsEarned / $creditsEnrolled) * 100;

        if ($average >= 10.00 && $creditPercentage >= 100) {
            return DecisionType::VALIDATED;
        } elseif ($average >= 9.00 && $creditPercentage >= 80) {
            return DecisionType::COMPENSATION;
        } elseif ($average < 8.00 || $creditPercentage < 50) {
            return DecisionType::FAILED;
        } else {
            return DecisionType::RESIT_REQUIRED;
        }
    }
}
