<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromotion extends Model
{
    use HasFactory;

    protected $fillable = [

        // Student
        'student_id',

        // From Details
        'from_academic_year_id',
        'from_class_id',
        'from_section_id',

        // To Details
        'to_academic_year_id',
        'to_class_id',
        'to_section_id',

        // Promotion Details
        'promotion_date',
        'promotion_type',
        'result_status',

        // Academic Performance
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'gpa',

        // Other Info
        'remarks',
        'is_processed',

        // Audit
        'created_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    public function fromClass()
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    public function fromSection()
    {
        return $this->belongsTo(Section::class, 'from_class_id');
    }

    public function toAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    public function toClass()
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    public function toSection()
    {
        return $this->belongsTo(Section::class, 'to_section_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}