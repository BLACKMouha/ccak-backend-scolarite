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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('course_unit_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('credits');
            $table->unsignedSmallInteger('hours_lecture')->default(0);
            $table->unsignedSmallInteger('hours_td')->default(0);
            $table->unsignedSmallInteger('hours_tp')->default(0);
            $table->decimal('coefficient', 6, 2)->default(1);
            $table->json('prerequisites')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('course_unit_id')->references('id')->on('course_units')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
