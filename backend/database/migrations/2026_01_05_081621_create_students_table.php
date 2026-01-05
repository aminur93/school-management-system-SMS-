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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 20)->unique()->comment('Auto-generated unique student ID');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('medium_id');
            $table->unsignedBigInteger('current_class_id')->nullable();
            $table->unsignedBigInteger('current_section_id')->nullable();
            $table->unsignedBigInteger('current_academic_year_id')->nullable();
            $table->string('admission_number', 50)->unique();
            $table->date('admission_date');

            // Personal Information
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->default('Bangladeshi');

            // Contact Information
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('present_address');
            $table->text('permanent_address');

            // Academic Information
            $table->string('roll_number', 20)->nullable();

            // Previous School Information
            $table->string('previous_school_name', 200)->nullable();
            $table->string('previous_class', 50)->nullable();

            // Status
            $table->enum('status', ['Active', 'Transferred', 'Dropout', 'TC_Issued', 'Completed'])
                  ->default('Active');

            // Profile
            $table->string('profile_photo', 255)->nullable();
            $table->string('profile_photo_url', 255)->nullable();
            $table->string('birth_certificate_no', 50)->nullable();
            $table->string('birth_certificate_no_url', 50)->nullable();

            // Timestamps
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index('student_id');
            $table->index('admission_number');
            $table->index('status');
            $table->index(['current_class_id', 'current_section_id']);
            $table->index('medium_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};