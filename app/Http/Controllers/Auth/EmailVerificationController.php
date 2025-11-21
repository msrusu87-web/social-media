<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Send email verification notification.
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        SendVerificationEmail::dispatch($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent'
        ]);
    }

    /**
     * Verify email address.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect('/')->with('error', 'Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('info', 'Email already verified');
        }

        $user->markEmailAsVerified();

        return redirect('/')->with('success', 'Email verified successfully');
    }

    /**
     * Resend verification email.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        SendVerificationEmail::dispatch($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Verification email resent'
        ]);
    }
}
