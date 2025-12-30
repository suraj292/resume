<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResumeAnalysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'input_type',
        'file_name',
        'file_path',
        'file_size',
        'pasted_content',
        'ats_score',
        'score_grade',
        'matched_keywords',
        'missing_keywords',
        'formatting_checks',
        'content_analysis',
        'critical_issues',
        'experience_level',
        'word_count',
        'keyword_match_percentage',
        'improvement_suggestions',
        'raw_ai_response',
        'ip_address',
    ];

    protected $casts = [
        'matched_keywords' => 'array',
        'missing_keywords' => 'array',
        'formatting_checks' => 'array',
        'content_analysis' => 'array',
        'critical_issues' => 'array',
        'ats_score' => 'integer',
        'file_size' => 'integer',
        'word_count' => 'integer',
        'keyword_match_percentage' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getScoreColorAttribute(): string
    {
        return match(true) {
            $this->ats_score >= 80 => 'green',
            $this->ats_score >= 60 => 'yellow',
            $this->ats_score >= 40 => 'orange',
            default => 'red'
        };
    }

    public function getScoreGradeAttribute(): string
    {
        return match(true) {
            $this->ats_score >= 80 => 'Excellent',
            $this->ats_score >= 60 => 'Good',
            $this->ats_score >= 40 => 'Needs Improvement',
            default => 'Poor'
        };
    }
}
