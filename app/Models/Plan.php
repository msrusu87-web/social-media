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
        'stripe_plan_id',
        'price',
        'interval',
        'description',
        'features',
        'posts_limit',
        'ai_credits',
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
            'features' => 'array',
            'price' => 'decimal:2',
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
}
