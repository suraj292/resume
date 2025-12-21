<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Exchange rate: $1 = ₹84 (approximate)
     */
    public function run(): void
    {
        // Example pricing structure:
        // Free Plan: $0 / ₹0
        // Pro Plan: $6/month, $60/year / ₹499/month, ₹4999/year  
        // Career+ Plan: $12/month, $120/year / ₹999/month, ₹9999/year

        $plans = [
            [
                'slug' => 'free',
                'monthly_price_usd' => 0,
                'yearly_price_usd' => 0,
                'monthly_price_inr' => 0,
                'yearly_price_inr' => 0,
            ],
            [
                'slug' => 'pro',
                'monthly_price_usd' => 6,
                'yearly_price_usd' => 60,  // Save ~17%
                'monthly_price_inr' => 499,
                'yearly_price_inr' => 4999,  // Save ~17%
            ],
            [
                'slug' => 'career-plus',
                'monthly_price_usd' => 12,
                'yearly_price_usd' => 120,  // Save ~17%
                'monthly_price_inr' => 999,
                'yearly_price_inr' => 9999,  // Save ~17%
            ],
        ];

        foreach ($plans as $planData) {
            Plan::where('slug', $planData['slug'])->update([
                'monthly_price_usd' => $planData['monthly_price_usd'],
                'yearly_price_usd' => $planData['yearly_price_usd'],
                'monthly_price_inr' => $planData['monthly_price_inr'],
                'yearly_price_inr' => $planData['yearly_price_inr'],
            ]);
        }

        $this->command->info('Plan prices updated successfully!');
    }
}
