<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->user->hasVerifiedEmail()) {
                $this->user->notify(new VerifyEmail());
                Log::info("Verification email sent to user {$this->user->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send verification email to user {$this->user->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
