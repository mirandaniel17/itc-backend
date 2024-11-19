<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Searchable, Notifiable;

    protected $fillable = [
        'last_name',
        'second_last_name',
        'name',
        'ci',
        'image',
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

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function toSearchableArray()
    {
        return [
            'last_name' => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'name' => $this->name,
            'ci' => $this->ci,
            'placeofbirth' => $this->placeofbirth,
            'phone' => $this->phone,
        ];
    }
}
