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
                'price' => 0.00,
                'billing_period' => 'monthly',
                'features' => [
                    '5 posts per month',
                    '2 social platforms',
                    'Basic scheduling',
                    'Community support',
                ],
                'max_posts_per_month' => 5,
                'max_platforms' => 2,
                'ai_content' => false,
                'analytics' => false,
                'scheduling' => true,
                'is_active' => true,
                'stripe_price_id' => null,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Ideal for individuals and small businesses',
                'price' => 9.99,
                'billing_period' => 'monthly',
                'features' => [
                    '30 posts per month',
                    '4 social platforms',
                    'Advanced scheduling',
                    'Basic analytics',
                    'Email support',
                ],
                'max_posts_per_month' => 30,
                'max_platforms' => 4,
                'ai_content' => false,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
                'stripe_price_id' => env('STRIPE_PRICE_STARTER'),
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Best for growing businesses and content creators',
                'price' => 29.99,
                'billing_period' => 'monthly',
                'features' => [
                    '100 posts per month',
                    'All social platforms',
                    'AI content generation',
                    'Advanced analytics',
                    'Priority support',
                    'Team collaboration',
                ],
                'max_posts_per_month' => 100,
                'max_platforms' => 6,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
                'stripe_price_id' => env('STRIPE_PRICE_PROFESSIONAL'),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations with advanced needs',
                'price' => 99.99,
                'billing_period' => 'monthly',
                'features' => [
                    'Unlimited posts',
                    'All social platforms',
                    'Advanced AI features',
                    'Custom analytics & reporting',
                    'Dedicated account manager',
                    'API access',
                    'White-label options',
                ],
                'max_posts_per_month' => null,
                'max_platforms' => 6,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
                'stripe_price_id' => env('STRIPE_PRICE_ENTERPRISE'),
            ],
            [
                'name' => 'Professional Yearly',
                'slug' => 'professional-yearly',
                'description' => 'Professional plan billed annually (Save 20%)',
                'price' => 287.88,
                'billing_period' => 'yearly',
                'features' => [
                    '100 posts per month',
                    'All social platforms',
                    'AI content generation',
                    'Advanced analytics',
                    'Priority support',
                    'Team collaboration',
                    '2 months free',
                ],
                'max_posts_per_month' => 100,
                'max_platforms' => 6,
                'ai_content' => true,
                'analytics' => true,
                'scheduling' => true,
                'is_active' => true,
                'stripe_price_id' => env('STRIPE_PRICE_PROFESSIONAL_YEARLY'),
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('Plans seeded successfully!');
    }
}
