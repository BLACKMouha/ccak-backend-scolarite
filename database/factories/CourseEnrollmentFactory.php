<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\CourseEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseEnrollmentFactory extends Factory
{
    protected $model = CourseEnrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => fn() => \App\Models\Student::factory(),
            'course_id' => fn() => \App\Models\Course::factory(),
        ];
    }
}
