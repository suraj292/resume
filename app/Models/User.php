<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'currency',
        'plan_id',
        'plan_started_at',
        'plan_expiry',
        'resumes_created',
        'ats_scans_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'plan_started_at' => 'datetime',
            'plan_expiry' => 'datetime',
            'resumes_created' => 'integer',
            'ats_scans_used' => 'integer',
        ];
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the user's current plan.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if user can create a resume.
     */
    public function canCreateResume(): bool
    {
        if (!$this->plan) {
            return false;
        }
        
        $limit = $this->plan->resume_limit;
        return $limit === -1 || $this->resumes_created < $limit;
    }

    /**
     * Check if user can use ATS checker.
     */
    public function canUseATS(): bool
    {
        if (!$this->plan) {
            return false;
        }
        
        $limit = $this->plan->ats_scan_limit;
        return $limit === -1 || $this->ats_scans_used < $limit;
    }

    /**
     * Check if user has access to a specific template.
     */
    public function hasAccessToTemplate(int $templateId): bool
    {
        if (!$this->plan) {
            return false;
        }
        
        return $this->plan->templates()->where('template_id', $templateId)->exists();
    }

    /**
     * Get remaining resume count.
     */
    public function getResumesRemainingAttribute(): int|string
    {
        if (!$this->plan || $this->plan->resume_limit === -1) {
            return 'unlimited';
        }
        
        return max(0, $this->plan->resume_limit - $this->resumes_created);
    }

    /**
     * Get remaining ATS scans count.
     */
    public function getAtsScansRemainingAttribute(): int|string
    {
        if (!$this->plan || $this->plan->ats_scan_limit === -1) {
            return 'unlimited';
        }
        
        return max(0, $this->plan->ats_scan_limit - $this->ats_scans_used);
    }

    /**
     * Get the registration method for the user.
     */
    public function getRegistrationMethodAttribute(): string
    {
        if ($this->socialAccounts()->exists()) {
            $providers = $this->socialAccounts->pluck('provider')->unique();
            return $providers->count() > 1 ? 'Multiple' : ucfirst($providers->first());
        }

        return 'Email';
    }
}
