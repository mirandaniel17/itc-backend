<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Teacher extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'last_name',
        'second_last_name',
        'name',
        'ci',
        'dateofbirth',
        'placeofbirth',
        'phone',
        'gender',
        'specialty',
    ];

    protected $casts = [
        'dateofbirth' => 'date',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function toSearchableArray()
    {
        return [
            'last_name' => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'name' => $this->name,
            'placeofbirth' => $this->placeofbirth,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'specialty' => $this->specialty,
        ];
    }
}
