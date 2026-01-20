<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDropout extends Model
{
    use HasFactory;

    protected $fillable = [

        // Student
        'student_id',

        // Academic Information
        'academic_year_id',
        'school_class_id',
        'section_id',

        // Dropout Details
        'dropout_date',
        'reason',
        'reason_details',

        // Academic Status
        'last_attendance_date',
        'total_working_days',
        'total_present_days',
        'attendance_percentage',

        // Fees Status
        'fees_due',
        'fees_cleared',

        // Follow-up
        'contacted_for_return',
        'contact_date',
        'willing_to_return',

        // Other
        'remarks',

        // Audit
        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear() 
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year');    
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