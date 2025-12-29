<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 students with associated user accounts
        Student::factory()
            ->count(20)
            ->create()
            ->each(function ($student) {
                // Create a user for each student
                $user = User::factory()->create([
                    'email' => strtolower(str_replace(' ', '.', $student->full_name)) . '@student.example.com',
                    'user_type' => 'STUDENT',
                    'is_active' => true,
                ]);

                // Assign STUDENT role to the user
                $user->assignRole('STUDENT');
            });
    }
}
