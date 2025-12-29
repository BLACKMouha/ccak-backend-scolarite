<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            // Utilisation de l'UUID comme clé primaire
            $table->uuid('id')->primary();

            // Relations (Clés étrangères)
            $table->uuid('student_id')->index();
            $table->uuid('reviewed_by')->nullable()->index();

            // Types énumérés
            $table->enum('type', [
                'CNI',
                'BIRTH_CERT',
                'BAC_DIPLOMA',
                'TRANSCRIPT',
                'PHOTO',
                'MEDICAL'
            ]);

            $table->enum('status', [
                'PENDING',
                'APPROVED',
                'REJECTED'
            ])->default('PENDING');

            // Fichiers et métadonnées
            $table->string('file_path');
            $table->string('file_name');
            $table->text('notes')->nullable();

            // Dates et Timestamps
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps(); // Génère created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
