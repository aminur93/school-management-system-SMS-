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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            // Foreign Keys
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('section_id');

            // Academic Info
            $table->string('roll_number', 20)->nullable();

            // Enrollment Details
            $table->date('enrollment_date');
            $table->enum('enrollment_status', [
                'Enrolled',
                'Promoted',
                'Detained',
                'Transferred',
                'Completed'
            ])->default('Enrolled');

            // Fees
            $table->decimal('total_fees', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('scholarship_amount', 10, 2)->nullable();
            $table->decimal('net_fees', 10, 2)->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // who is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(
                ['student_id', 'academic_year_id'],
                'uq_student_academic_year'
            );

            $table->index(
                ['academic_year_id', 'school_class_id', 'section_id'],
                'idx_academic_class_section'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};