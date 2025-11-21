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

class PublishToInstagram implements ShouldQueue
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
            // Instagram requires media
            if (empty($this->post->media)) {
                throw new \Exception('Instagram posts require media');
            }

            $url = "https://graph.facebook.com/v18.0/{$this->connection->platform_user_id}/media";

            $data = [
                'caption' => $this->post->content,
                'image_url' => $this->post->media[0], // First media item
                'access_token' => $this->connection->access_token,
            ];

            $response = Http::post($url, $data);

            if ($response->successful()) {
                $creationId = $response->json('id');
                
                // Publish the media
                $publishUrl = "https://graph.facebook.com/v18.0/{$this->connection->platform_user_id}/media_publish";
                Http::post($publishUrl, [
                    'creation_id' => $creationId,
                    'access_token' => $this->connection->access_token,
                ]);

                Log::info("Post {$this->post->id} published to Instagram successfully");
            } else {
                throw new \Exception($response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to Instagram: " . $e->getMessage());
            throw $e;
        }
    }
}
