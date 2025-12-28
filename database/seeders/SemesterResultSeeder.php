<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemesterResult;

class SemesterResultSeeder extends Seeder
{
    public function run(): void
    {
        SemesterResult::factory()->count(20)->create();
    }
}
