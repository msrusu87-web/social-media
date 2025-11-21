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
                'post_limit' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Great for individuals and small businesses',
                'price' => 9.99,
                'post_limit' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Advanced features for growing businesses',
                'price' => 29.99,
                'post_limit' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited posting for large organizations',
                'price' => 99.99,
                'post_limit' => -1,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
