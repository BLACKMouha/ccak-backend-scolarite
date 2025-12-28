<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseEnrollment;
use App\Models\Student;
use App\Models\Course;

class CourseEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No students or courses found. Skipping course enrollments.');
            return;
        }

        // Enroll each student in 3-7 random courses
        $students->each(function ($student) use ($courses) {
            $numberOfCourses = rand(3, 7);
            $selectedCourses = $courses->random(min($numberOfCourses, $courses->count()));

            foreach ($selectedCourses as $course) {
                CourseEnrollment::firstOrCreate([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                ]);
            }
        });

        $this->command->info('Course enrollments created successfully.');
    }
}
