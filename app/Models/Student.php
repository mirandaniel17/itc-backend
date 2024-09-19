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
        'first_name',
        'second_name',
        'dateofbirth',
        'placeofbirth',
        'phone',
        'gender',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*public function enrollments()
    {
        return $this->hasMany(StudentCourseSchedule::class);
    }*/

    protected $casts = [
        'dateofbirth' => 'date',
        'status' => 'boolean',
    ];
}
