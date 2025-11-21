<?php

namespace Database\Seeders;

use App\Models\Plan;
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
                'description' => 'Perfect for getting started with social media management',
                'price' => 0,
                'billing_period' => 'monthly',
                'posts_per_month' => 10,
                'social_accounts' => 1,
                'ai_content' => false,
                'analytics' => false,
                'scheduling' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Great for individuals and small businesses',
                'price' => 9.99,
                'billing_period' => 'monthly',
                'posts_per_month' => 50,
                'social_accounts' => 3,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses and agencies',
                'price' => 29.99,
                'billing_period' => 'monthly',
                'posts_per_month' => 200,
                'social_accounts' => 10,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited power for large organizations',
                'price' => 99.99,
                'billing_period' => 'monthly',
                'posts_per_month' => 0, // Unlimited
                'social_accounts' => 50,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
