<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class DualCurrencyPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This creates separate plan records for USD and INR
     */
    public function run(): void
    {
        // Clear existing plans to avoid duplicates
        Plan::query()->delete();

        $plans = [
            // FREE PLAN - USD
            [
                'name' => 'Free',
                'slug' => 'free-usd',
                'description' => 'For getting started',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => '$',
                'currency_code' => 'USD',
                'features' => [
                    '1 Resume',
                    'Limited Templates',
                    'Basic ATS Score',
                    'Resume Upload'
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            // FREE PLAN - INR
            [
                'name' => 'Free',
                'slug' => 'free-inr',
                'description' => 'For getting started',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => '₹',
                'currency_code' => 'INR',
                'features' => [
                    '1 Resume',
                    'Limited Templates',
                    'Basic ATS Score',
                    'Resume Upload'
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            
            // PRO PLAN - USD
            [
                'name' => 'Pro',
                'slug' => 'pro-usd',
                'description' => 'Best for job seekers',
                'monthly_price' => 6,
                'yearly_price' => 60,
                'currency' => '$',
                'currency_code' => 'USD',
                'features' => [
                    'Unlimited Resumes',
                    'All Premium Templates',
                    'AI Resume Optimization',
                    'ATS Keyword Matching',
                    'PDF & DOCX Downloads',
                    'Priority Support'
                ],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            // PRO PLAN - INR
            [
                'name' => 'Pro',
                'slug' => 'pro-inr',
                'description' => 'Best for job seekers',
                'monthly_price' => 499,
                'yearly_price' => 4999,
                'currency' => '₹',
                'currency_code' => 'INR',
                'features' => [
                    'Unlimited Resumes',
                    'All Premium Templates',
                    'AI Resume Optimization',
                    'ATS Keyword Matching',
                    'PDF & DOCX Downloads',
                    'Priority Support'
                ],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // CAREER+ PLAN - USD
            [
                'name' => 'Career+',
                'slug' => 'career-plus-usd',
                'description' => 'For serious professionals',
                'monthly_price' => 12,
                'yearly_price' => 120,
                'currency' => '$',
                'currency_code' => 'USD',
                'features' => [
                    'Everything in Pro',
                    'Advanced ATS Analysis',
                    'Job Description Matcher',
                    'Cover Letter Generator',
                    'Personal Branding Themes',
                    'Early Access Features'
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            // CAREER+ PLAN - INR
            [
                'name' => 'Career+',
                'slug' => 'career-plus-inr',
                'description' => 'For serious professionals',
                'monthly_price' => 999,
                'yearly_price' => 9999,
                'currency' => '₹',
                'currency_code' => 'INR',
                'features' => [
                    'Everything in Pro',
                    'Advanced ATS Analysis',
                    'Job Description Matcher',
                    'Cover Letter Generator',
                    'Personal Branding Themes',
                    'Early Access Features'
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('Dual currency plans created successfully!');
        $this->command->info('Total plans: ' . Plan::count());
    }
}
