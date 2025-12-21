<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class UpdatePlanLimitsSeeder extends Seeder
{
    /**
     * Update existing plans with limits and features
     */
    public function run(): void
    {
        $planUpdates = [
            'free-usd' => [
                'resume_limit' => 1,
                'ats_scan_limit' => 3,
                'ai_optimization' => false,
                'cover_letter' => false,
            ],
            'free-inr' => [
                'resume_limit' => 1,
                'ats_scan_limit' => 3,
                'ai_optimization' => false,
                'cover_letter' => false,
            ],
            'pro-usd' => [
                'resume_limit' => -1, // unlimited
                'ats_scan_limit' => -1, // unlimited
                'ai_optimization' => true,
                'cover_letter' => false,
            ],
            'pro-inr' => [
                'resume_limit' => -1,
                'ats_scan_limit' => -1,
                'ai_optimization' => true,
                'cover_letter' => false,
            ],
            'career-plus-usd' => [
                'resume_limit' => -1,
                'ats_scan_limit' => -1,
                'ai_optimization' => true,
                'cover_letter' => true,
            ],
            'career-plus-inr' => [
                'resume_limit' => -1,
                'ats_scan_limit' => -1,
                'ai_optimization' => true,
                'cover_letter' => true,
            ],
        ];

        foreach ($planUpdates as $slug => $limits) {
            Plan::where('slug', $slug)->update($limits);
        }

        $this->command->info('Plan limits updated successfully!');
    }
}
