<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display available plans.
     */
    public function index(): View
    {
        $plans = Plan::active()->get();
        $currentSubscription = Auth::user()->subscription;

        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request, Plan $plan): RedirectResponse
    {
        $user = Auth::user();

        // Check if user already has an active subscription
        if ($user->hasActiveSubscription()) {
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
    public function cancel(): RedirectResponse
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found');
        }

        try {
            $this->stripeService->cancelSubscription($subscription);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription canceled successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume canceled subscription.
     */
    public function resume(): RedirectResponse
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        if (!$subscription || !$subscription->isCanceled()) {
            return back()->with('error', 'No canceled subscription found');
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
     * Change subscription plan.
     */
    public function change(Request $request, Plan $plan): RedirectResponse
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found');
        }

        try {
            $this->stripeService->changeSubscription($subscription, $plan);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription plan changed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change plan: ' . $e->getMessage());
        }
    }
}
