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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('academic_program_id');
            $table->uuid('academic_year_id');
            $table->unsignedSmallInteger('current_semester')->default(1);
            $table->enum('status', ['PENDING', 'REGISTERED', 'ACTIVE', 'COMPLETED', 'WITHDRAWN'])->default('PENDING');
            $table->date('enrollment_date');
            $table->decimal('registration_fee_paid', 10, 2)->default(0);
            $table->boolean('is_scholarship')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_program_id')->references('id')->on('academic_programs')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();

            $table->unique(['student_id', 'academic_program_id', 'academic_year_id'], 'enrollment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment');
    }
};
