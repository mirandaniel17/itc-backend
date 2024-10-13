<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Modality extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['name', 'duration_in_months'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }
}
