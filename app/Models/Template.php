<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'html_content',
        'is_premium',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the plans that have access to this template.
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }

    /**
     * Scope to get only active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only free templates.
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope to get only premium templates.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }
}
