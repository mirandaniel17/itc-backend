<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Payment extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_date',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function toSearchableArray()
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
