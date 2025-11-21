<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Customer as StripeCustomer;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new subscription for a user.
     */
    public function createSubscription(User $user, Plan $plan): Subscription
    {
        // Create or get Stripe customer
        $customer = $this->getOrCreateCustomer($user);

        // Create Stripe subscription
        $stripeSubscription = StripeSubscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => $plan->stripe_price_id],
            ],
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ]);

        // Create local subscription record
        return $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'status' => $stripeSubscription->status,
            'current_period_start' => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
            'current_period_end' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
        ]);
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Subscription $subscription): Subscription
    {
        $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
        $stripeSubscription->cancel();

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        return $subscription;
    }

    /**
     * Resume a canceled subscription.
     */
    public function resumeSubscription(Subscription $subscription): Subscription
    {
        $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
        $stripeSubscription->resume();

        $subscription->update([
            'status' => 'active',
            'canceled_at' => null,
        ]);

        return $subscription;
    }

    /**
     * Change subscription plan.
     */
    public function changeSubscription(Subscription $subscription, Plan $newPlan): Subscription
    {
        $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);

        // Update the subscription items
        StripeSubscription::update($subscription->stripe_subscription_id, [
            'items' => [
                [
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $newPlan->stripe_price_id,
                ],
            ],
            'proration_behavior' => 'always_invoice',
        ]);

        $subscription->update([
            'plan_id' => $newPlan->id,
        ]);

        return $subscription;
    }

    /**
     * Handle webhook from Stripe.
     */
    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'];
        $data = $payload['data']['object'];

        switch ($event) {
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($data);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($data);
                break;
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($data);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($data);
                break;
        }
    }

    /**
     * Get or create Stripe customer for user.
     */
    protected function getOrCreateCustomer(User $user): StripeCustomer
    {
        // Check if user already has a Stripe customer ID stored
        if ($user->stripe_customer_id) {
            return StripeCustomer::retrieve($user->stripe_customer_id);
        }

        // Create new Stripe customer
        $customer = StripeCustomer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        // Store customer ID on user
        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    protected function handleSubscriptionUpdated(array $data): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $data['id'])->first();
        if ($subscription) {
            $subscription->update([
                'status' => $data['status'],
                'current_period_start' => date('Y-m-d H:i:s', $data['current_period_start']),
                'current_period_end' => date('Y-m-d H:i:s', $data['current_period_end']),
            ]);
        }
    }

    protected function handleSubscriptionDeleted(array $data): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $data['id'])->first();
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    protected function handlePaymentSucceeded(array $data): void
    {
        // Handle successful payment
        // Could send confirmation email, etc.
    }

    protected function handlePaymentFailed(array $data): void
    {
        // Handle failed payment
        // Could send notification to user, etc.
    }
}
