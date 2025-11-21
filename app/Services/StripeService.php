<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a new subscription.
     */
    public function createSubscription(User $user, Plan $plan): Subscription
    {
        try {
            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($user);

            // Create the subscription
            $stripeSubscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $plan->stripe_plan_id],
                ],
                'trial_period_days' => 14,
            ]);

            // Save subscription to database
            return $user->subscription()->create([
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => $stripeSubscription->status,
                'trial_ends_at' => $stripeSubscription->trial_end ? 
                    now()->createFromTimestamp($stripeSubscription->trial_end) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe subscription creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Subscription $subscription): void
    {
        try {
            $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);

            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe subscription cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(Subscription $subscription): void
    {
        try {
            $this->stripe->subscriptions->update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $subscription->update([
                'status' => 'active',
                'ends_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe subscription resume failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook($request): void
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);

            switch ($event->type) {
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get or create a Stripe customer.
     */
    protected function getOrCreateCustomer(User $user)
    {
        if ($user->stripe_customer_id) {
            return $this->stripe->customers->retrieve($user->stripe_customer_id);
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    /**
     * Handle subscription updated event.
     */
    protected function handleSubscriptionUpdated($stripeSubscription): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription->status,
            ]);
        }
    }

    /**
     * Handle subscription deleted event.
     */
    protected function handleSubscriptionDeleted($stripeSubscription): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
        }
    }

    /**
     * Handle payment succeeded event.
     */
    protected function handlePaymentSucceeded($invoice): void
    {
        // Log successful payment
        Log::info('Payment succeeded for invoice: ' . $invoice->id);
    }

    /**
     * Handle payment failed event.
     */
    protected function handlePaymentFailed($invoice): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'past_due',
            ]);
        }

        Log::warning('Payment failed for invoice: ' . $invoice->id);
    }
}
