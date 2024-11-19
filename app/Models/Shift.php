<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Shift extends Model
{
    use HasFactory, Searchable;

    protected $fillable =
        [
            'name',
            'start_time',
            'end_time',
            'room_id'
        ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function courseSchedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }



    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
