<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deliberation_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('academic_program_id');
            $table->uuid('academic_year_id');
            $table->unsignedTinyInteger('semester');
            $table->string('session_name');
            $table->date('session_date');
            $table->enum('status', ['SCHEDULED', 'IN_PROGRESS', 'COMPLETED', 'CLOSED'])->default('SCHEDULED');
            $table->uuid('presided_by');
            $table->json('jury_members')->nullable();
            $table->timestamps();

            $table->foreign('academic_program_id')->references('id')->on('academic_programs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('presided_by')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['academic_program_id', 'academic_year_id', 'semester']);
            $table->index('status');
            $table->index('session_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliberation_sessions');
    }
};
