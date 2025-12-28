<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseEnrollment;

class CourseEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        CourseEnrollment::factory()->count(20)->create();
    }
}
