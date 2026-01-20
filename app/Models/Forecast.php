<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'forecast_date',
        'amount',
        'confidence_score',
        'insights',
        'category',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'amount' => 'decimal:2',
        'confidence_score' => 'decimal:2',
    ];
}
