<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'course_id', 
        'shift_id', 
        'day'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
