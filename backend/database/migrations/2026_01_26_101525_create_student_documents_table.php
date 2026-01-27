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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');

            $table->enum('document_type', [
                'Birth_Certificate',
                'Previous_School_TC',
                'Medical_Certificate',
                'Photo',
                'Parent_NID',
                'Address_Proof',
                'Other'
            ]);

            $table->string('document_name', 200);
            $table->string('document_path', 255)->nullable();
            $table->string('document_url', 255)->nullable();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('document_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};