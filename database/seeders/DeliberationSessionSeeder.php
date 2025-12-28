<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\AcademicYear;
use App\Models\DeliberationSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliberationSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer des données existantes ou créer des fakes
        $academicPrograms = AcademicProgram::all();
        $academicYears = AcademicYear::all();
        $users = User::all();

        if ($academicPrograms->isEmpty() || $academicYears->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  Academic Programs, Academic Years, or Users not found. Creating sample data...');

            // Créer des données de test si nécessaire
            if ($academicPrograms->isEmpty()) {
                $academicPrograms = AcademicProgram::factory()->count(3)->create();
            }
            if ($academicYears->isEmpty()) {
                $academicYears = AcademicYear::factory()->count(2)->create();
            }
            if ($users->isEmpty()) {
                $users = User::factory()->count(5)->create();
            }
        }

        // Créer 20 sessions de délibération
        DeliberationSession::factory()
            ->count(20)
            ->create([
                'academic_program_id' => fn() => $academicPrograms->random()->id,
                'academic_year_id' => fn() => $academicYears->random()->id,
                'presided_by' => fn() => $users->random()->id,
            ])
            ->each(function ($session) use ($users) {
                // Ajouter des membres de jury aléatoires (2-5 membres)
                $juryMembers = $users->random(rand(2, 5))->pluck('id')->toArray();
                $session->update(['jury_members' => $juryMembers]);
            });

        $this->command->info('✅ Created 20 DeliberationSessions with jury members');
    }
}
