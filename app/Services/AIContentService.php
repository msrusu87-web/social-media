<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIContentService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }

    /**
     * Generate content using AI.
     */
    public function generateContent(string $prompt, string $platform, string $tone = 'casual', string $length = 'medium'): string
    {
        $lengthInstructions = match ($length) {
            'short' => 'Keep it brief and concise (1-2 sentences).',
            'medium' => 'Make it engaging and informative (2-4 sentences).',
            'long' => 'Provide detailed and comprehensive content (4-6 sentences).',
            default => 'Make it engaging and informative.',
        };

        $platformInstructions = match ($platform) {
            'facebook' => 'Optimize for Facebook with engaging questions or calls to action.',
            'instagram' => 'Create Instagram-friendly content with emphasis on visual descriptions.',
            'twitter' => 'Keep it concise for Twitter/X (under 280 characters), punchy and engaging.',
            'tiktok' => 'Make it energetic and trending, suitable for short-form video.',
            'youtube' => 'Create compelling content suitable for video descriptions.',
            'pinterest' => 'Focus on visual appeal and DIY/how-to style content.',
            default => 'Create engaging social media content.',
        };

        $systemPrompt = "You are an expert social media content creator. Generate {$tone} content for {$platform}. {$lengthInstructions} {$platformInstructions}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'gpt-4',
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

        throw new \Exception('Failed to generate content: ' . $response->body());
    }

    /**
     * Generate hashtags for content.
     */
    public function generateHashtags(string $content, int $count = 5): array
    {
        $systemPrompt = "You are an expert in social media hashtags. Generate {$count} relevant, popular hashtags for the given content. Return only the hashtags, one per line, each starting with #.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $content],
            ],
            'temperature' => 0.5,
            'max_tokens' => 200,
        ]);

        if ($response->successful()) {
            $hashtagsText = $response->json('choices.0.message.content');
            $hashtags = array_filter(array_map('trim', explode("\n", $hashtagsText)));
            return array_values($hashtags);
        }

        throw new \Exception('Failed to generate hashtags: ' . $response->body());
    }

    /**
     * Improve existing content.
     */
    public function improveContent(string $content, string $improvementType): string
    {
        $instructions = match ($improvementType) {
            'grammar' => 'Fix any grammar, spelling, and punctuation errors while maintaining the original tone and message.',
            'engagement' => 'Rewrite to make it more engaging, compelling, and likely to generate interactions.',
            'clarity' => 'Improve clarity and readability while maintaining the original message.',
            'brevity' => 'Make it more concise and impactful while keeping the core message.',
            default => 'Improve the overall quality of the content.',
        };

        $systemPrompt = "You are an expert content editor. {$instructions}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $content],
            ],
            'temperature' => 0.5,
            'max_tokens' => 500,
        ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content');
        }

        throw new \Exception('Failed to improve content: ' . $response->body());
    }
}
