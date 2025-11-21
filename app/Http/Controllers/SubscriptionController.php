<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        protected StripeService $stripeService
    ) {}

    /**
     * Display subscription management page
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $currentSubscription = $user->subscription;
        $plans = Plan::where('is_active', true)->get();

        return Inertia::render('Subscription/Manage', [
            'currentSubscription' => $currentSubscription,
            'plans' => $plans,
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            $subscription = $this->stripeService->createSubscription(
                $request->user(),
                $plan,
                $request->input('payment_method')
            );

            return redirect()->route('subscription.index')
                ->with('success', 'Successfully subscribed to ' . $plan->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $subscription = $request->user()->subscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found');
        }

        try {
            $this->stripeService->cancelSubscription($subscription);

            return redirect()->route('subscription.index')
                ->with('success', 'Subscription canceled successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Update subscription plan
     */
    public function changePlan(Request $request, Plan $plan)
    {
        $subscription = $request->user()->subscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found');
        }

        try {
            $this->stripeService->updateSubscription($subscription, $plan);

            return redirect()->route('subscription.index')
                ->with('success', 'Plan updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update plan: ' . $e->getMessage());
        }
    }
}
