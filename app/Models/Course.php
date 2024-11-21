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
        'parallel',
        'description',
        'cost',
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

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }


    public function courseSchedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function calculateStudentAverage($studentId)
    {
        return $this->tasks()
            ->join('grades', 'tasks.id', '=', 'grades.task_id')
            ->where('grades.student_id', $studentId)
            ->avg('grades.grade');
    }


    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'cost' => $this->cost,
        ];
    }
}
