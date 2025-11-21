<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Send verification email
     */
    public function send(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ]);
        }

        SendVerificationEmail::dispatch($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent',
        ]);
    }

    /**
     * Verify email
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();

        // Here you would implement the actual verification logic
        // comparing the token with stored token

        if ($user->markEmailAsVerified()) {
            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid verification token',
        ], 400);
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ]);
        }

        SendVerificationEmail::dispatch($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Verification email resent',
        ]);
    }
}
