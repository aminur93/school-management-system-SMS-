<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'school_class_id',
        'name',
        'capacity',
        'room_number',
        'is_active',
        'created_by',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}