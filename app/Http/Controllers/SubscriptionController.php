<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display available plans.
     */
    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        $currentSubscription = auth()->user()->subscription;

        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $user = $request->user();

        // Check if user already has an active subscription
        if ($user->subscription && $user->subscription->isActive()) {
            return back()->with('error', 'You already have an active subscription');
        }

        try {
            $subscription = $this->stripeService->createSubscription($user, $plan);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Successfully subscribed to ' . $plan->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $subscription = $request->user()->subscription;

        if (!$subscription || !$subscription->isActive()) {
            return back()->with('error', 'No active subscription found');
        }

        try {
            $this->stripeService->cancelSubscription($subscription);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription cancelled successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume cancelled subscription.
     */
    public function resume(Request $request)
    {
        $subscription = $request->user()->subscription;

        if (!$subscription || $subscription->status !== 'canceled') {
            return back()->with('error', 'No cancelled subscription found');
        }

        try {
            $this->stripeService->resumeSubscription($subscription);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription resumed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook.
     */
    public function webhook(Request $request)
    {
        try {
            $this->stripeService->handleWebhook($request);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
