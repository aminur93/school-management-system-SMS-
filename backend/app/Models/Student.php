<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [

        // Identifiers
        'student_id',
        'school_id',
        'medium_id',
        'current_class_id',
        'current_section_id',
        'current_academic_year_id',
        'admission_number',
        'admission_date',

        // Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'nationality',

        // Contact Information
        'email',
        'phone',
        'present_address',
        'permanent_address',

        // Academic Information
        'roll_number',

        // Previous School Information
        'previous_school_name',
        'previous_class',

        // Status
        'status',

        // Profile
        'profile_photo',
        'profile_photo_url',
        'birth_certificate_no',
        'birth_certificate_no_url',

        // Audit
        'created_by',
        'updated_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'current_class_id');
    }

    public function medium()
    {
        return $this->belongsTo(Medium::class, 'medium_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'current_section_id');
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class, 'current_academic_year_id');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->profile_photo) {
                ImageUpload::deleteApplicationStorage($model->profile_photo);
            }
            if ($model->birth_certificate_no) {
                ImageUpload::deleteApplicationStorage($model->birth_certificate_no);
            }
        });
    }
}