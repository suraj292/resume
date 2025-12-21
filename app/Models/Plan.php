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
        'monthly_price_usd',
        'yearly_price_usd',
        'monthly_price_inr',
        'yearly_price_inr',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
        'currency',
        'currency_code',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'monthly_price_usd' => 'decimal:2',
        'yearly_price_usd' => 'decimal:2',
        'monthly_price_inr' => 'decimal:2',
        'yearly_price_inr' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_monthly_price',
        'formatted_yearly_price',
    ];

    /**
     * Get the formatted monthly price based on currency.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        // Use currency-specific price if available
        if ($this->currency === '$' && $this->monthly_price_usd) {
            return '$ ' . number_format($this->monthly_price_usd, 0);
        } elseif ($this->currency === '₹' && $this->monthly_price_inr) {
            return '₹ ' . number_format($this->monthly_price_inr, 0);
        }
        
        // Fallback to generic price
        return $this->currency . ' ' . number_format($this->monthly_price, 0);
    }

    /**
     * Get the formatted yearly price based on currency.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        // Use currency-specific price if available
        if ($this->currency === '$' && $this->yearly_price_usd) {
            return '$ ' . number_format($this->yearly_price_usd, 0);
        } elseif ($this->currency === '₹' && $this->yearly_price_inr) {
            return '₹ ' . number_format($this->yearly_price_inr, 0);
        }
        
        // Fallback to generic price
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
