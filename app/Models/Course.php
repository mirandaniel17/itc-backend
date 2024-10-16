<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Course extends Model
{
    use HasFactory, Searchable;

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

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }
}
