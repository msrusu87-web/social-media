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

class PublishToPinterest implements ShouldQueue
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
            // Pinterest requires image
            if (empty($this->post->media)) {
                throw new \Exception('Pinterest posts require image media');
            }

            $url = "https://api.pinterest.com/v5/pins";

            $data = [
                'title' => substr($this->post->content, 0, 100),
                'description' => $this->post->content,
                'media_source' => [
                    'source_type' => 'image_url',
                    'url' => $this->post->media[0],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->connection->access_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info("Post {$this->post->id} published to Pinterest successfully");
            } else {
                throw new \Exception($response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Pinterest: " . $e->getMessage());
            throw $e;
        }
    }
}
