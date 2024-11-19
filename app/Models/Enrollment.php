<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'discount_id',
        'document_1',
        'document_2',
        'enrollment_date',
        'payment_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function totalPayments()
    {
        return $this->payments()->sum('amount');
    }

    public function isFullyPaid()
    {
        return $this->totalPayments() >= $this->course->cost;
    }
}
