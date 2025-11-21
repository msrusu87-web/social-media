<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'features',
        'max_posts_per_month',
        'max_platforms',
        'ai_content',
        'analytics',
        'scheduling',
        'is_active',
        'stripe_price_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price' => 'decimal:2',
            'ai_content' => 'boolean',
            'analytics' => 'boolean',
            'scheduling' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include monthly plans.
     */
    public function scopeMonthly($query)
    {
        return $query->where('billing_period', 'monthly');
    }

    /**
     * Scope a query to only include yearly plans.
     */
    public function scopeYearly($query)
    {
        return $query->where('billing_period', 'yearly');
    }
}
