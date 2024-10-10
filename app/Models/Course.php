<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'start_date', 
        'end_date', 
        'teacher_id', 
        'modality_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class);
    }
}
