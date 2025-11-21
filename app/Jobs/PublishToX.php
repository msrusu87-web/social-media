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

class PublishToX implements ShouldQueue
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
            $url = "https://api.twitter.com/2/tweets";

            $data = [
                'text' => $this->post->content,
            ];

            if (!empty($this->post->media)) {
                // Upload media first and get media IDs
                $data['media'] = [
                    'media_ids' => $this->uploadMedia()
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->connection->access_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info("Post {$this->post->id} published to X successfully");
            } else {
                throw new \Exception($response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to X: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload media to X.
     */
    protected function uploadMedia()
    {
        // Implement media upload logic
        return [];
    }
}
