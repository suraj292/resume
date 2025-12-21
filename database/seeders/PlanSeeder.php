<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'For getting started',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => '₹',
                'features' => [
                    '1 Resume',
                    'Limited Templates',
                    'Basic ATS Score',
                    'Resume Upload',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Best for job seekers',
                'monthly_price' => 499,
                'yearly_price' => 4999,
                'currency' => '₹',
                'features' => [
                    'Unlimited Resumes',
                    'All Premium Templates',
                    'AI Resume Optimization',
                    'ATS Keyword Matching',
                    'PDF & DOCX Downloads',
                    'Priority Support',
                ],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Career+',
                'slug' => 'career-plus',
                'description' => 'For serious professionals',
                'monthly_price' => 999,
                'yearly_price' => 9999,
                'currency' => '₹',
                'features' => [
                    'Everything in Pro',
                    'Advanced ATS Analysis',
                    'Job Description Matcher',
                    'Cover Letter Generator',
                    'Personal Branding Themes',
                    'Early Access Features',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
