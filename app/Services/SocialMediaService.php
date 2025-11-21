<?php

namespace App\Services;

use App\Jobs\PublishToFacebook;
use App\Jobs\PublishToInstagram;
use App\Jobs\PublishToPinterest;
use App\Jobs\PublishToTikTok;
use App\Jobs\PublishToX;
use App\Jobs\PublishToYouTube;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * Publish a post to selected platforms.
     */
    public function publishPost(Post $post): void
    {
        if (empty($post->platforms)) {
            return;
        }

        $user = $post->user;

        foreach ($post->platforms as $platform) {
            $connection = $user->socialConnections()
                ->where('platform', $platform)
                ->where('is_active', true)
                ->first();

            if (!$connection) {
                Log::warning("No active connection for platform {$platform} for post {$post->id}");
                continue;
            }

            $this->dispatchPublishJob($post, $connection, $platform);
        }

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Dispatch the appropriate publish job for a platform.
     */
    protected function dispatchPublishJob(Post $post, $connection, string $platform): void
    {
        $jobClass = match ($platform) {
            'facebook' => PublishToFacebook::class,
            'instagram' => PublishToInstagram::class,
            'x' => PublishToX::class,
            'tiktok' => PublishToTikTok::class,
            'youtube' => PublishToYouTube::class,
            'pinterest' => PublishToPinterest::class,
            default => null,
        };

        if ($jobClass) {
            dispatch(new $jobClass($post, $connection));
        }
    }

    /**
     * Get OAuth URL for a platform.
     */
    public function getAuthUrl(string $platform): string
    {
        return match ($platform) {
            'facebook' => $this->getFacebookAuthUrl(),
            'instagram' => $this->getInstagramAuthUrl(),
            'x' => $this->getXAuthUrl(),
            'tiktok' => $this->getTikTokAuthUrl(),
            'youtube' => $this->getYouTubeAuthUrl(),
            'pinterest' => $this->getPinterestAuthUrl(),
            default => throw new \Exception("Unsupported platform: {$platform}"),
        };
    }

    /**
     * Get Facebook OAuth URL.
     */
    protected function getFacebookAuthUrl(): string
    {
        $clientId = config('services.facebook.client_id');
        $redirectUri = route('social.callback', 'facebook');
        $scopes = 'pages_manage_posts,pages_read_engagement';

        return "https://www.facebook.com/v18.0/dialog/oauth?client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scopes}";
    }

    /**
     * Get Instagram OAuth URL (uses Facebook OAuth).
     */
    protected function getInstagramAuthUrl(): string
    {
        return $this->getFacebookAuthUrl();
    }

    /**
     * Get X (Twitter) OAuth URL.
     */
    protected function getXAuthUrl(): string
    {
        $clientId = config('services.twitter.client_id');
        $redirectUri = route('social.callback', 'x');

        return "https://twitter.com/i/oauth2/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope=tweet.read+tweet.write+users.read";
    }

    /**
     * Get TikTok OAuth URL.
     */
    protected function getTikTokAuthUrl(): string
    {
        $clientId = config('services.tiktok.client_id');
        $redirectUri = route('social.callback', 'tiktok');

        return "https://www.tiktok.com/auth/authorize?client_key={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope=user.info.basic,video.upload";
    }

    /**
     * Get YouTube OAuth URL.
     */
    protected function getYouTubeAuthUrl(): string
    {
        $clientId = config('services.google.client_id');
        $redirectUri = route('social.callback', 'youtube');
        $scopes = 'https://www.googleapis.com/auth/youtube.upload';

        return "https://accounts.google.com/o/oauth2/v2/auth?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope={$scopes}";
    }

    /**
     * Get Pinterest OAuth URL.
     */
    protected function getPinterestAuthUrl(): string
    {
        $clientId = config('services.pinterest.client_id');
        $redirectUri = route('social.callback', 'pinterest');
        $scopes = 'pins:read,pins:write,boards:read,boards:write';

        return "https://www.pinterest.com/oauth/?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope={$scopes}";
    }
}
