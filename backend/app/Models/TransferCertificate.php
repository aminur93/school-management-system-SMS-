<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferCertificate extends Model
{
    use HasFactory;

    protected $fillable = [

        // Student
        'student_id',
        'tc_number',

        // Academic Info
        'academic_year_id',
        'school_class_id',
        'section_id',

        // TC Details
        'issue_date',
        'leaving_date',
        'reason',
        'reason_details',

        // Character & Conduct
        'persion_character',
        'conduct',

        // Academic Performance
        'last_exam_passed',
        'last_exam_result',
        'total_working_days',
        'total_present_days',
        'attendance_percentage',

        // New School Info
        'new_school_name',
        'new_school_address',

        // Documents
        'tc_document_path',
        'tc_document_path_url',

        // Status
        'status',

        // Approvals
        'requested_by',
        'approved_by',
        'approved_at',
        'issued_by',

        // Remarks
        'remarks',

        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}