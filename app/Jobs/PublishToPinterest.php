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

class PublishToPinterest implements ShouldQueue
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
                ->where('platform', 'pinterest')
                ->active()
                ->first();

            if (!$connection) {
                Log::warning("No Pinterest connection found for user {$this->post->user->id}");
                return;
            }

            $socialService->publishToPinterest($this->post, $connection);

            Log::info("Published post {$this->post->id} to Pinterest");
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Pinterest: {$e->getMessage()}");
            throw $e;
        }
    }
}
