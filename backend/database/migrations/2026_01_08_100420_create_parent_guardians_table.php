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
        Schema::create('parent_guardians', function (Blueprint $table) {
            $table->id();
            // Foreign key
            $table->unsignedBigInteger('student_id');

            // Relation info
            $table->enum('relation_type', ['Father', 'Mother', 'Guardian', 'Other']);
            $table->boolean('is_primary')
                ->default(false)
                ->comment('Primary contact person');

            // Personal Information
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('nid_number', 20)->nullable()
                ->comment('National ID');
            $table->date('date_of_birth')->nullable();

            // Contact Information
            $table->string('email', 100)->nullable();
            $table->string('phone', 20);
            $table->string('alternate_phone', 20)->nullable();
            $table->text('address')->nullable();

            // Professional Information
            $table->string('occupation', 100)->nullable();
            $table->string('organization', 200)->nullable();
            $table->string('designation', 100)->nullable();
            $table->decimal('annual_income', 15, 2)->nullable();
            $table->text('office_address')->nullable();

            // Emergency Contact
            $table->boolean('is_emergency_contact')->default(false);

            // Documents
            $table->string('photo', 255)->nullable();
            $table->string('photo_url', 255)->nullable();
            $table->string('nid_photo', 255)->nullable();
            $table->string('nid_photo_url', 255)->nullable();

            // who is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index(['student_id', 'is_primary']);
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_guardians');
    }
};