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

class PublishToTikTok implements ShouldQueue
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
            // TikTok requires video
            if (empty($this->post->media)) {
                throw new \Exception('TikTok posts require video media');
            }

            $url = "https://open.tiktokapis.com/v2/post/publish/video/init/";

            $data = [
                'post_info' => [
                    'title' => substr($this->post->content, 0, 150),
                    'privacy_level' => 'PUBLIC_TO_EVERYONE',
                ],
                'source_info' => [
                    'source' => 'FILE_UPLOAD',
                    'video_url' => $this->post->media[0],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->connection->access_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info("Post {$this->post->id} published to TikTok successfully");
            } else {
                throw new \Exception($response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->post->id} to TikTok: " . $e->getMessage());
            throw $e;
        }
    }
}
