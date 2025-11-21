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
        'posts_per_month',
        'social_accounts',
        'ai_content',
        'analytics',
        'scheduling',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'ai_content' => 'boolean',
            'analytics' => 'boolean',
            'scheduling' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all subscriptions for the plan.
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
     * Check if plan has unlimited posts.
     */
    public function hasUnlimitedPosts(): bool
    {
        return $this->posts_per_month === 0;
    }
}
