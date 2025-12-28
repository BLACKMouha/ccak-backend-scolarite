<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\GeneratedDocument;

class GeneratedDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $users = User::all();

        if ($students->isEmpty()) {
            $students = Student::factory()->count(10)->create();
        }

        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }
        GeneratedDocument::factory()
            ->count(20)
            ->state(function () use ($students, $users) {
                return [
                    'student_id' => $students->random()->id,
                    'generated_by' => $users->random()->id,
                ];
            })
            ->create();
    }
}
