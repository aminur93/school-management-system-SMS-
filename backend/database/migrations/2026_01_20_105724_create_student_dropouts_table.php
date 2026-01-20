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
        Schema::create('student_dropouts', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('student_id');

            // Academic Information
            $table->unsignedInteger('academic_year_id');
            $table->unsignedInteger('school_class_id');
            $table->unsignedInteger('section_id')->nullable();

            // Dropout Details
            $table->date('dropout_date');
            $table->enum('reason', [
                'Financial',
                'Personal',
                'Health',
                'Relocation',
                'Poor_Performance',
                'Other'
            ]);
            $table->text('reason_details')->nullable();

            // Academic Status
            $table->date('last_attendance_date')->nullable();
            $table->integer('total_working_days')->nullable();
            $table->integer('total_present_days')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // Fees Status
            $table->decimal('fees_due', 10, 2)->nullable();
            $table->boolean('fees_cleared')->default(false);

            // Follow-up
            $table->boolean('contacted_for_return')->default(false);
            $table->date('contact_date')->nullable();
            $table->boolean('willing_to_return')->nullable();

            $table->text('remarks')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index('student_id');
            $table->index('dropout_date');
            $table->index('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_dropouts');
    }
};