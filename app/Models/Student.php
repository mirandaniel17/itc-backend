<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Student extends Model
{
    use HasFactory, Searchable;

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

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'ci' => $this->ci,
            'program_type' => $this->program_type,
            'school_cycle' => $this->school_cycle,
            'shift' => $this->shift,
            'parallel' => $this->parallel,
            'phone' => $this->phone,
            'gender' => $this->gender,
        ];
    }
}
