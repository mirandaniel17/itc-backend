<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
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
        'specialty',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*public function courses()
    {
        return $this->hasMany(Course::class);
    }*/

    protected $casts = [
        'dateofbirth' => 'date',
    ];
}
