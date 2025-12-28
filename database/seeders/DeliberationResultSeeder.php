<?php

namespace Database\Seeders;

use App\Models\DeliberationResult;
use App\Models\DeliberationSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeliberationResultSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = DeliberationSession::all();

        if ($sessions->isEmpty()) {
            $this->command->warn('⚠️  No DeliberationSessions found. Creating sample sessions...');
            $sessions = DeliberationSession::factory()->count(5)->create();
        }

        // Créer 10-20 résultats par session
        $totalResults = 0;

        foreach ($sessions as $session) {
            $numberOfResults = rand(10, 20);

            DeliberationResult::factory()
                ->count($numberOfResults)
                ->create([
                    'deliberation_session_id' => $session->id,
                    'student_id' => fn() => Str::uuid()->toString(), // UUID externe
                ]);

            $totalResults += $numberOfResults;
        }

        $this->command->info("✅ Created {$totalResults} DeliberationResults across {$sessions->count()} sessions");
    }
}
