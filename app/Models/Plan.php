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
        'resume_limit',
        'ats_scan_limit',
        'ai_optimization',
        'cover_letter',
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
        'ai_optimization' => 'boolean',
        'cover_letter' => 'boolean',
    ];

    protected $appends = [
        'formatted_monthly_price',
        'formatted_yearly_price',
        'comparison_features',
    ];

    /**
     * Get the templates associated with this plan.
     */
    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }

    /**
     * Get the users subscribed to this plan.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get comparison features for pricing table.
     */
    public function getComparisonFeaturesAttribute(): array
    {
        return [
            'resumes' => $this->resume_limit == -1 ? 'Unlimited' : $this->resume_limit,
            'ai_optimization' => $this->ai_optimization,
            'cover_letter' => $this->cover_letter,
            'ats_scans' => $this->ats_scan_limit == -1 ? 'Unlimited' : $this->ats_scan_limit,
            'export_formats' => $this->monthly_price == 0 ? 'TXT' : 'PDF, DOCX',
        ];
    }

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
