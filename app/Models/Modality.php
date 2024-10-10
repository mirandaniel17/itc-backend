<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'duration_in_months'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
