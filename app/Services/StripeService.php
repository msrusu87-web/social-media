<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a new subscription
     */
    public function createSubscription(User $user, Plan $plan, string $paymentMethod): Subscription
    {
        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'payment_method' => $paymentMethod,
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod,
                ],
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // Create subscription in Stripe
        $stripeSubscription = $this->stripe->subscriptions->create([
            'customer' => $user->stripe_customer_id,
            'items' => [
                ['price' => $plan->stripe_price_id],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Create subscription record
        $subscription = $user->subscription()->create([
            'plan_id' => $plan->id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return $subscription;
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Subscription $subscription): void
    {
        $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);

        $subscription->update([
            'status' => 'canceled',
            'ends_at' => now(),
        ]);
    }

    /**
     * Update subscription to a new plan
     */
    public function updateSubscription(Subscription $subscription, Plan $newPlan): Subscription
    {
        $stripeSubscription = $this->stripe->subscriptions->retrieve($subscription->stripe_subscription_id);

        $this->stripe->subscriptions->update($subscription->stripe_subscription_id, [
            'items' => [
                [
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $newPlan->stripe_price_id,
                ],
            ],
        ]);

        $subscription->update([
            'plan_id' => $newPlan->id,
        ]);

        return $subscription->fresh();
    }
}
