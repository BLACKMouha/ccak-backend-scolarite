<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseUnit;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 course units first if none exist
        if (CourseUnit::count() === 0) {
            CourseUnit::factory()->count(10)->create();
        }

        // Get existing course units
        $courseUnits = CourseUnit::all();

        // Create 5 courses for each course unit
        $courseUnits->each(function ($courseUnit) {
            Course::factory()->count(5)->create([
                'course_unit_id' => $courseUnit->id,
            ]);
        });
    }
}
