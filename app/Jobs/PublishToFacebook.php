<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublishToFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    protected $connection;

    /**
     * Create a new job instance.
     */
    public function __construct(Post $post, $connection)
    {
        $this->post = $post;
        $this->connection = $connection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = "https://graph.facebook.com/v18.0/{$this->connection->platform_user_id}/feed";

            $data = [
                'message' => $this->post->content,
                'access_token' => $this->connection->access_token,
            ];

            if (!empty($this->post->media)) {
                // Handle media attachments
                $data['attached_media'] = $this->uploadMedia();
            }

            $response = Http::post($url, $data);

            if ($response->successful()) {
                Log::info("Post {$this->post->id} published to Facebook successfully");
            } else {
                throw new \Exception($response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Facebook: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload media to Facebook.
     */
    protected function uploadMedia()
    {
        // Implement media upload logic
        return [];
    }
}
