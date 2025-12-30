<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $connection = 'mysql_shard_0'; // shard connection
    protected $table = 'school_classes';
    
    protected $fillable = [
        'medium_id',
        'name',
        'code',
        'order_number',
        'is_active',
        'created_by'
    ];
}