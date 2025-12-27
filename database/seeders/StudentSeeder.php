<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Document;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR'); // Utiliser le français pour des données réalistes

        // Créer quelques admins pour les reviews de documents
        $admins = [];
        for ($k = 0; $k < 5; $k++) {
            $user = User::factory()->create([
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
            ]);
            $admin = Admin::create([
                'user_id' => $user->id,
                'full_name' => $faker->name(),
            ]);
            $admins[] = $admin;
        }

        for ($i = 0; $i < 100; $i++) {
            // Créer un utilisateur pour l'étudiant
            $user = User::factory()->create([
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
            ]);

            // Créer l'étudiant
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => Student::generateStudentNumber(),
                'full_name' => $faker->name(),
                'gender' => $faker->randomElement(['M', 'F']),
                'date_of_birth' => $faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
                'place_of_birth' => $faker->city(),
                'nationality' => 'Sénégalaise', // Supposons Sénégal
                'phone' => $faker->phoneNumber(),
                'emergency_contact_name' => $faker->name(),
                'emergency_contact_phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'photo_url' => null, // Peut être ajouté plus tard
                'status' => $faker->randomElement(['ACTIVE', 'ACTIVE', 'ACTIVE', 'SUSPENDED', 'GRADUATED']), // Plus de ACTIVE
            ]);

            // Créer 1 à 2 tuteurs
            $numGuardians = $faker->numberBetween(1, 2);
            for ($j = 0; $j < $numGuardians; $j++) {
                Guardian::create([
                    'student_id' => $student->id,
                    'full_name' => $faker->name(),
                    'relationship' => $faker->randomElement(['FATHER', 'MOTHER', 'GUARDIAN']),
                    'phone' => $faker->phoneNumber(),
                    'email' => $faker->email(),
                    'address' => $faker->address(),
                    'occupation' => $faker->jobTitle(),
                ]);
            }

            // Créer des documents avec différents statuts
            $documentTypes = ['CNI', 'BIRTH_CERT', 'BAC_DIPLOMA', 'TRANSCRIPT', 'PHOTO', 'MEDICAL'];
            $numDocuments = $faker->numberBetween(3, 6); // 3 à 6 documents par étudiant
            $selectedTypes = $faker->randomElements($documentTypes, $numDocuments, false);

            foreach ($selectedTypes as $type) {
                $status = $faker->randomElement(['PENDING', 'APPROVED', 'REJECTED']);
                $reviewedBy = null;
                $reviewedAt = null;
                $notes = null;

                if ($status !== 'PENDING') {
                    $reviewedBy = $faker->randomElement($admins)->id;
                    $reviewedAt = $faker->dateTimeBetween('-1 month', 'now');
                    if ($status === 'REJECTED') {
                        $notes = $faker->sentence();
                    }
                }

                Document::create([
                    'student_id' => $student->id,
                    'type' => $type,
                    'file_path' => "documents/{$type}_{$student->id}.pdf", // Chemin fictif
                    'file_name' => "{$type}_{$student->id}.pdf",
                    'status' => $status,
                    'reviewed_by' => $reviewedBy,
                    'notes' => $notes,
                    'uploaded_at' => $faker->dateTimeBetween('-2 months', 'now'),
                    'reviewed_at' => $reviewedAt,
                ]);
            }
        }
    }
}
