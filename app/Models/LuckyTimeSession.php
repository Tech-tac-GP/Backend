<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeActiveNow(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where('start_time', '<=', now())
                     ->where('end_time', '>=', now());
    }
}