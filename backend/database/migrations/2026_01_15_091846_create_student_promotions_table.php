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
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            // Student
            $table->unsignedBigInteger('student_id');

            // From Details
            $table->unsignedBigInteger('from_academic_year_id');
            $table->unsignedBigInteger('from_class_id');
            $table->unsignedBigInteger('from_section_id');

            // To Details
            $table->unsignedBigInteger('to_academic_year_id');
            $table->unsignedBigInteger('to_class_id');
            $table->unsignedBigInteger('to_section_id')->nullable();

            // Promotion Details
            $table->date('promotion_date');
            $table->enum('promotion_type', ['Promoted', 'Detained', 'Double_Promoted'])
                  ->default('Promoted');
            $table->enum('result_status', ['Pass', 'Fail', 'Conditional'])->nullable();

            // Academic Performance
            $table->decimal('total_marks', 10, 2)->nullable();
            $table->decimal('obtained_marks', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->decimal('gpa', 3, 2)->nullable();

            // Other Info
            $table->text('remarks')->nullable();
            $table->boolean('is_processed')->default(false);

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('student_id');
            $table->index(
                ['from_academic_year_id', 'to_academic_year_id'],
                'idx_student_promotion_years'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};