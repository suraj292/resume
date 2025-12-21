<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
        'currency',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_monthly_price',
        'formatted_yearly_price',
    ];

    /**
     * Get the formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->monthly_price, 0);
    }

    /**
     * Get the formatted yearly price.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->yearly_price, 0);
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular plans.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
