<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    public const UPDATED_AT = null;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'price',
        'stock_quantity',
        'status',
        'main_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope for text searching by name or description.
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        if (trim($searchTerm) === '') {
            return $query;
        }

        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope for filtering by exact matches (category, brand, status).
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['category_id'] ?? null, fn($q, $id) => $q->where('category_id', $id))
              ->when($filters['brand_id'] ?? null, fn($q, $id) => $q->where('brand_id', $id))
              ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status));

        return $query;
    }

    /**
     * Append the Lucky Time discount calculations directly to this model instance.
     */
    public function applyLuckyTimeDiscount(?LuckyTimeSession $session): void
    {
        $discount = $session ? (float) $session->discount_percentage : 0;

        $this->discount_percentage = $discount;
        $this->discounted_price = $discount > 0
            ? round((float) $this->price * (1 - ($discount / 100)), 2)
            : (float) $this->price;
    }
}