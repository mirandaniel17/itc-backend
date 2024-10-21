<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Discount extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['name', 'percentage'];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'percentage' => $this->percentage,
        ];
    }
}
