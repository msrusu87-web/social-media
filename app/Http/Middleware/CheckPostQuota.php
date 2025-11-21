<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPostQuota
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $subscription = $user->subscription;

        if (!$subscription) {
            // Check if user has free tier limits
            $postsThisMonth = $user->posts()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            if ($postsThisMonth >= 10) { // Free tier limit
                return response()->json([
                    'error' => 'Post quota exceeded. Please upgrade your plan.',
                ], 403);
            }

            return $next($request);
        }

        $plan = $subscription->plan;
        $postLimit = $plan->post_limit;

        // Unlimited posts
        if ($postLimit === -1) {
            return $next($request);
        }

        $postsThisMonth = $user->posts()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($postsThisMonth >= $postLimit) {
            return response()->json([
                'error' => 'Post quota exceeded for your plan. Please upgrade.',
            ], 403);
        }

        return $next($request);
    }
}
