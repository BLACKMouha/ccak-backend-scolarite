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
        Schema::create('semester_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('student_id');
            $table->uuid('academic_year_id');
            $table->integer('semester');
            $table->decimal('total_credits_enrolled', 5, 2);
            $table->decimal('total_credits_earned', 5, 2);
            $table->decimal('semester_average', 5, 2);
            $table->decimal('semester_gpa', 5, 2);
            $table->enum('decision', ['VALIDATED', 'COMPENSATION', 'FAILED', 'RESIT_REQUIRED']);
            $table->uuid('calculated_by');
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('calculated_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_results');
    }
};
