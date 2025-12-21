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
            // INR Plans
            [
                'name' => 'Free',
                'slug' => 'free-inr',
                'description' => 'Forever Free - Perfect for getting started',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => '₹',
                'features' => [
                    'Build 1 resume',
                    '2 basic templates (Modern, Simple)',
                    'Core resume sections',
                    'Manual content editing',
                    'PDF export with watermark',
                    '1 ATS scan per month',
                    'Standard fonts & layouts',
                    'Basic contact details',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro-inr',
                'description' => 'Recommended - Best for students & professionals',
                'monthly_price' => 249,
                'yearly_price' => 2499,
                'currency' => '₹',
                'features' => [
                    'Unlimited resumes',
                    '6 premium templates',
                    'AI resume bullet rewriting',
                    'AI keyword enhancement',
                    'Unlimited ATS score checks',
                    'Export PDF & DOCX (no watermark)',
                    'Custom color themes',
                    'Resume tailoring for job roles',
                    'Real-time ATS improvement tips',
                    'Skill suggestions',
                    'Email support (48-hour response)',
                ],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Career+',
                'slug' => 'career-plus-inr',
                'description' => 'Premium - For serious career advancement',
                'monthly_price' => 499,
                'yearly_price' => 4999,
                'currency' => '₹',
                'features' => [
                    'Everything in Pro, plus:',
                    'AI cover letter generator',
                    'AI resume generation from job description',
                    'Multiple resume versions (role-based)',
                    'Advanced ATS keyword optimization',
                    'LinkedIn headline & summary optimization',
                    'Interview question suggestions',
                    'Resume performance insights',
                    'Export: PDF, DOCX',
                    'Remove branding',
                    'Priority support (24-36 hrs)',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],

            // USD Plans
            [
                'name' => 'Free',
                'slug' => 'free-usd',
                'description' => 'Forever Free - Try before you buy',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => '$',
                'features' => [
                    'Build 1 resume',
                    '2 basic templates',
                    'Core resume sections',
                    'Manual editing',
                    'PDF export with watermark',
                    '1 ATS scan per month',
                    'Standard fonts/layouts',
                    'Basic contact info',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro-usd',
                'description' => 'Most Popular - Perfect for job seekers',
                'monthly_price' => 8.99,
                'yearly_price' => 79,
                'currency' => '$',
                'features' => [
                    'Unlimited resumes',
                    '6 premium templates',
                    'AI bullet point enhancement',
                    'AI keyword suggestions',
                    'Unlimited ATS checks',
                    'Export PDF & DOCX (no watermark)',
                    'Custom color themes',
                    'Real-time ATS optimization tips',
                    'Skill gap recommendations',
                    'Standard support',
                ],
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Career+',
                'slug' => 'career-plus-usd',
                'description' => 'Premium - For power users & frequent applicants',
                'monthly_price' => 14.99,
                'yearly_price' => 129,
                'currency' => '$',
                'features' => [
                    'Everything in Pro, plus:',
                    'AI cover letter generator',
                    'AI resume creation from job description',
                    'LinkedIn profile optimization',
                    'Multiple resume versions',
                    'Advanced ATS keyword optimization',
                    'Interview prep suggestions',
                    'Resume analytics & insights',
                    'Export: PDF, DOCX, TXT',
                    'Custom branding removal',
                    'Priority support',
                ],
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 6,
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
