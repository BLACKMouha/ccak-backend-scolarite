<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        Grade::factory()->count(20)->create();
    }
}
