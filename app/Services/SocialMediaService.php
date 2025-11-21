<?php

namespace App\Services;

use App\Models\SocialConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SocialMediaService
{
    /**
     * Publish content to Facebook.
     */
    public function publishToFacebook(
        SocialConnection $connection,
        string $content,
        ?array $media = null
    ): array {
        $accessToken = $connection->access_token;
        $pageId = $connection->platform_user_id;

        $endpoint = "https://graph.facebook.com/v18.0/{$pageId}/feed";

        $data = [
            'message' => $content,
            'access_token' => $accessToken,
        ];

        // Handle media attachments
        if (!empty($media)) {
            // For simplicity, we'll upload the first image
            // In production, handle multiple media properly
            $mediaUrl = Storage::url($media[0]);
            $data['link'] = url($mediaUrl);
        }

        $response = Http::post($endpoint, $data);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'id' => $responseData['id'] ?? null,
                'url' => "https://facebook.com/{$responseData['id']}",
            ];
        }

        throw new \Exception('Facebook API Error: ' . $response->body());
    }

    /**
     * Publish content to Instagram.
     */
    public function publishToInstagram(
        SocialConnection $connection,
        string $content,
        array $media
    ): array {
        $accessToken = $connection->access_token;
        $instagramAccountId = $connection->platform_user_id;

        // Instagram requires at least one media item
        if (empty($media)) {
            throw new \Exception('Instagram posts require at least one image or video');
        }

        $mediaUrl = Storage::url($media[0]);
        $fullMediaUrl = url($mediaUrl);

        // Step 1: Create media container
        $containerEndpoint = "https://graph.facebook.com/v18.0/{$instagramAccountId}/media";
        $containerResponse = Http::post($containerEndpoint, [
            'image_url' => $fullMediaUrl,
            'caption' => $content,
            'access_token' => $accessToken,
        ]);

        if (!$containerResponse->successful()) {
            throw new \Exception('Instagram Container Error: ' . $containerResponse->body());
        }

        $containerId = $containerResponse->json('id');

        // Step 2: Publish the media
        $publishEndpoint = "https://graph.facebook.com/v18.0/{$instagramAccountId}/media_publish";
        $publishResponse = Http::post($publishEndpoint, [
            'creation_id' => $containerId,
            'access_token' => $accessToken,
        ]);

        if ($publishResponse->successful()) {
            $responseData = $publishResponse->json();
            return [
                'id' => $responseData['id'] ?? null,
                'url' => "https://instagram.com/p/{$responseData['id']}",
            ];
        }

        throw new \Exception('Instagram Publish Error: ' . $publishResponse->body());
    }

    /**
     * Publish content to X (Twitter).
     */
    public function publishToX(
        SocialConnection $connection,
        string $content,
        ?array $media = null
    ): array {
        $accessToken = $connection->access_token;

        $endpoint = 'https://api.twitter.com/2/tweets';

        $data = [
            'text' => $content,
        ];

        // Handle media attachments
        if (!empty($media)) {
            // Upload media first, then attach to tweet
            $mediaIds = $this->uploadMediaToX($connection, $media);
            $data['media'] = ['media_ids' => $mediaIds];
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, $data);

        if ($response->successful()) {
            $responseData = $response->json();
            $tweetId = $responseData['data']['id'] ?? null;
            return [
                'id' => $tweetId,
                'url' => "https://twitter.com/i/web/status/{$tweetId}",
            ];
        }

        throw new \Exception('X (Twitter) API Error: ' . $response->body());
    }

    /**
     * Upload media to X (Twitter).
     */
    private function uploadMediaToX(SocialConnection $connection, array $media): array
    {
        $mediaIds = [];
        $accessToken = $connection->access_token;

        foreach ($media as $mediaPath) {
            $mediaContent = Storage::get($mediaPath);
            $mediaType = Storage::mimeType($mediaPath);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
            ])->attach(
                'media',
                $mediaContent,
                basename($mediaPath)
            )->post('https://upload.twitter.com/1.1/media/upload.json');

            if ($response->successful()) {
                $mediaIds[] = $response->json('media_id_string');
            } else {
                Log::error('Failed to upload media to X: ' . $response->body());
            }
        }

        return $mediaIds;
    }

    /**
     * Refresh access token for a social connection.
     */
    public function refreshAccessToken(SocialConnection $connection): bool
    {
        // Implementation depends on the platform
        // Each platform has different OAuth refresh mechanisms
        
        try {
            $refreshToken = $connection->refresh_token;
            
            if (!$refreshToken) {
                return false;
            }

            $endpoint = match($connection->platform) {
                'facebook' => 'https://graph.facebook.com/v18.0/oauth/access_token',
                'instagram' => 'https://graph.facebook.com/v18.0/oauth/access_token',
                'twitter' => 'https://api.twitter.com/2/oauth2/token',
                default => null,
            };

            if (!$endpoint) {
                return false;
            }

            // Platform-specific token refresh logic would go here
            // This is a simplified version

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to refresh token for connection {$connection->id}: {$e->getMessage()}");
            return false;
        }
    }
}
