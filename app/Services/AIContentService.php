<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIContentService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }

    /**
     * Generate content using AI.
     */
    public function generateContent(string $prompt, string $tone = 'casual', string $length = 'medium'): string
    {
        $lengthWords = [
            'short' => '50-100',
            'medium' => '100-200',
            'long' => '200-300',
        ];

        $systemPrompt = "You are a social media content creator. Generate engaging content with a {$tone} tone, approximately {$lengthWords[$length]} words.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            throw new \Exception('AI API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('AI content generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate hashtags for content.
     */
    public function generateHashtags(string $content, int $count = 5): array
    {
        $prompt = "Generate {$count} relevant hashtags for this social media content: {$content}. Return only the hashtags separated by commas, without the # symbol.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a social media hashtag expert.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
                'max_tokens' => 100,
            ]);

            if ($response->successful()) {
                $hashtags = $response->json('choices.0.message.content');
                return array_map(function ($tag) {
                    return '#' . trim($tag);
                }, explode(',', $hashtags));
            }

            throw new \Exception('AI API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Hashtag generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Optimize content for specific platform.
     */
    public function optimizeForPlatform(string $content, string $platform): string
    {
        $platformGuidelines = [
            'facebook' => 'Optimize for Facebook: engaging, conversational, emoji-friendly',
            'instagram' => 'Optimize for Instagram: visual focus, emoji-heavy, hashtag-rich',
            'x' => 'Optimize for X (Twitter): concise, punchy, under 280 characters',
            'tiktok' => 'Optimize for TikTok: trendy, fun, youth-oriented',
            'youtube' => 'Optimize for YouTube: descriptive, SEO-friendly',
            'pinterest' => 'Optimize for Pinterest: inspirational, visual-focused',
        ];

        $prompt = "{$platformGuidelines[$platform]}. Rewrite this content: {$content}";

        return $this->generateContent($prompt);
    }
}
