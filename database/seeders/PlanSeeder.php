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
                'stripe_plan_id' => null,
                'price' => 0.00,
                'interval' => 'month',
                'description' => 'Perfect for getting started with social media management',
                'features' => [
                    'Up to 3 social accounts',
                    '10 posts per month',
                    'Basic analytics',
                    'Post scheduling',
                ],
                'posts_limit' => 10,
                'ai_credits' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'stripe_plan_id' => 'price_starter_monthly',
                'price' => 19.99,
                'interval' => 'month',
                'description' => 'For individuals and small businesses',
                'features' => [
                    'Up to 5 social accounts',
                    '50 posts per month',
                    'Advanced analytics',
                    'Post scheduling',
                    'AI content generation',
                    'Priority support',
                ],
                'posts_limit' => 50,
                'ai_credits' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'stripe_plan_id' => 'price_professional_monthly',
                'price' => 49.99,
                'interval' => 'month',
                'description' => 'For growing businesses and teams',
                'features' => [
                    'Up to 15 social accounts',
                    '200 posts per month',
                    'Advanced analytics',
                    'Post scheduling',
                    'AI content generation',
                    'Team collaboration',
                    'White-label reports',
                    'Priority support',
                ],
                'posts_limit' => 200,
                'ai_credits' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'stripe_plan_id' => 'price_business_monthly',
                'price' => 99.99,
                'interval' => 'month',
                'description' => 'For agencies and large organizations',
                'features' => [
                    'Unlimited social accounts',
                    'Unlimited posts',
                    'Advanced analytics',
                    'Post scheduling',
                    'AI content generation',
                    'Team collaboration',
                    'White-label reports',
                    'API access',
                    'Dedicated account manager',
                    '24/7 support',
                ],
                'posts_limit' => 999999,
                'ai_credits' => 1000,
                'is_active' => true,
            ],
            [
                'name' => 'Starter Annual',
                'slug' => 'starter-annual',
                'stripe_plan_id' => 'price_starter_annual',
                'price' => 199.99,
                'interval' => 'year',
                'description' => 'Save 17% with annual billing',
                'features' => [
                    'Up to 5 social accounts',
                    '50 posts per month',
                    'Advanced analytics',
                    'Post scheduling',
                    'AI content generation',
                    'Priority support',
                ],
                'posts_limit' => 50,
                'ai_credits' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Annual',
                'slug' => 'professional-annual',
                'stripe_plan_id' => 'price_professional_annual',
                'price' => 499.99,
                'interval' => 'year',
                'description' => 'Save 17% with annual billing',
                'features' => [
                    'Up to 15 social accounts',
                    '200 posts per month',
                    'Advanced analytics',
                    'Post scheduling',
                    'AI content generation',
                    'Team collaboration',
                    'White-label reports',
                    'Priority support',
                ],
                'posts_limit' => 200,
                'ai_credits' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
