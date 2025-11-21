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

class PublishToInstagram implements ShouldQueue
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
            
            // Get Instagram connection
            $connection = $user->socialConnections()
                ->where('platform', 'instagram')
                ->active()
                ->first();

            if (!$connection) {
                throw new \Exception('No active Instagram connection found');
            }

            // Validate media for Instagram
            if (empty($this->post->media)) {
                throw new \Exception('Instagram posts require at least one image or video');
            }

            // Publish to Instagram
            $result = $service->publishToInstagram(
                $connection,
                $this->post->content,
                $this->post->media
            );

            // Update post with publish result
            $publishResults = $this->post->publish_results ?? [];
            $publishResults['instagram'] = [
                'status' => 'success',
                'post_id' => $result['id'] ?? null,
                'url' => $result['url'] ?? null,
                'published_at' => now()->toISOString(),
            ];

            $this->post->update([
                'publish_results' => $publishResults,
                'status' => 'published',
            ]);

            Log::info("Post {$this->post->id} published to Instagram successfully");

        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Instagram: {$e->getMessage()}");

            // Update post with error
            $publishResults = $this->post->publish_results ?? [];
            $publishResults['instagram'] = [
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
