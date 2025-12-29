<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medium extends Model
{

    protected $table = 'mediums';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'created_by'
    ];
}