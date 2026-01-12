<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentGuardian extends Model
{
     protected $fillable = [
        // Foreign key
        'student_id',

        // Relation info
        'relation_type',
        'is_primary',

        // Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'nid_number',
        'date_of_birth',

        // Contact Information
        'email',
        'phone',
        'alternate_phone',
        'address',

        // Professional Information
        'occupation',
        'organization',
        'designation',
        'annual_income',
        'office_address',

        // Emergency Contact
        'is_emergency_contact',

        // Documents
        'photo',
        'photo_url',
        'nid_photo',
        'nid_photo_url',

        'created_by'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}