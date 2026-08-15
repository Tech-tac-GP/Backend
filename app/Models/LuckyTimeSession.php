<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuckyTimeSession extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'start_time',
        'end_time',
        'discount_percentage',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'discount_percentage' => 'decimal:2',
    ];
}