<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\SocialMediaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishToFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SocialMediaService $socialService): void
    {
        try {
            $connection = $this->post->user->socialConnections()
                ->where('platform', 'facebook')
                ->active()
                ->first();

            if (!$connection) {
                Log::warning("No Facebook connection found for user {$this->post->user->id}");
                return;
            }

            $socialService->publishToFacebook($this->post, $connection);

            Log::info("Published post {$this->post->id} to Facebook");
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Facebook: {$e->getMessage()}");
            throw $e;
        }
    }
}
