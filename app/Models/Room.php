<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Room extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name'
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }
}
