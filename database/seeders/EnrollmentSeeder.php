<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        Enrollment::factory()->count(20)->create();
    }
}
