<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'second_last_name',
        'name',
        'image',
        'ci',
        'program_type',
        'school_cycle',
        'shift',
        'parallel',
        'dateofbirth',
        'placeofbirth',
        'phone',
        'gender',
        'status',
    ];

    protected $casts = [
        'dateofbirth' => 'date',
        'status' => 'boolean',
    ];
}
