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
        Schema::create('transfer_certificates', function (Blueprint $table) {
            $table->id();
             // Student
            $table->unsignedBigInteger('student_id');

            // TC Identity
            $table->string('tc_number', 50)->unique();

            // Academic Info
            $table->unsignedInteger('academic_year_id');
            $table->unsignedInteger('school_class_id');
            $table->unsignedInteger('section_id')->nullable();

            // TC Details
            $table->date('issue_date');
            $table->date('leaving_date');
            $table->enum('reason', [
                'Transfer',
                'Migration',
                'Personal',
                'Financial',
                'Other'
            ]);
            $table->text('reason_details')->nullable();

            // Character & Conduct
            $table->string('persion_character', 50)->nullable(); // Good, Very Good, Excellent
            $table->string('conduct', 50)->nullable();

            // Academic Performance
            $table->string('last_exam_passed', 100)->nullable();
            $table->string('last_exam_result', 50)->nullable();
            $table->integer('total_working_days')->nullable();
            $table->integer('total_present_days')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // New School Info
            $table->string('new_school_name', 200)->nullable();
            $table->text('new_school_address')->nullable();

            // Documents
            $table->string('tc_document_path', 255)->nullable();
            $table->string('tc_document_path_url', 255)->nullable();

            // Status
            $table->enum('status', [
                'Requested',
                'Approved',
                'Issued',
                'Cancelled'
            ])->default('Requested');

            // Approvals
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            //who created
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 

            // Indexes
            $table->index('student_id');
            $table->index('tc_number');
            $table->index('status');
            $table->index('issue_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_certificates');
    }
};