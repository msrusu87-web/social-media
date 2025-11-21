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

class PublishToX implements ShouldQueue
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
    public function handle(SocialMediaService $service): void
    {
        try {
            $user = $this->post->user;
            
            // Get X (Twitter) connection
            $connection = $user->socialConnections()
                ->where('platform', 'twitter')
                ->active()
                ->first();

            if (!$connection) {
                throw new \Exception('No active X (Twitter) connection found');
            }

            // Validate content length for X
            if (strlen($this->post->content) > 280) {
                throw new \Exception('Post content exceeds X (Twitter) character limit of 280');
            }

            // Publish to X
            $result = $service->publishToX(
                $connection,
                $this->post->content,
                $this->post->media
            );

            // Update post with publish result
            $publishResults = $this->post->publish_results ?? [];
            $publishResults['twitter'] = [
                'status' => 'success',
                'tweet_id' => $result['id'] ?? null,
                'url' => $result['url'] ?? null,
                'published_at' => now()->toISOString(),
            ];

            $this->post->update([
                'publish_results' => $publishResults,
                'status' => 'published',
            ]);

            Log::info("Post {$this->post->id} published to X (Twitter) successfully");

        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to X (Twitter): {$e->getMessage()}");

            // Update post with error
            $publishResults = $this->post->publish_results ?? [];
            $publishResults['twitter'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'attempted_at' => now()->toISOString(),
            ];

            $this->post->update([
                'publish_results' => $publishResults,
                'status' => 'failed',
            ]);

            throw $e;
        }
    }
}
