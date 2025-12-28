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
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->enum('type', [
                'TRANSCRIPT',
                'CERTIFICATE',
                'ATTESTATION',
                'ID_CARD',
                'DIPLOMA',
            ]);
            $table->string('document_number')->unique();
            $table->string('file_path');
            $table->uuid('generated_by');
            $table->json('metadata')->nullable();
            $table->enum('status', ['DRAFT', 'ISSUED', 'REVOKED'])->default('DRAFT');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('generated_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->index(['student_id', 'type']);
            $table->index(['document_number']);
            $table->index(['status', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
