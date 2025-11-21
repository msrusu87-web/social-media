<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIContentService
{
    private string $apiKey;
    private string $apiEndpoint;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->apiEndpoint = config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions');
    }

    /**
     * Generate content based on a prompt.
     */
    public function generateContent(
        string $prompt,
        string $tone = 'professional',
        string $length = 'medium',
        array $platforms = []
    ): string {
        $systemPrompt = $this->buildSystemPrompt($tone, $length, $platforms);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint, [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => $this->getMaxTokens($length),
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            throw new \Exception('Failed to generate content: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('AI Content Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Improve existing content.
     */
    public function improveContent(string $content, string $instruction): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint, [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional content editor. Improve the given content based on the user\'s instructions while maintaining the core message.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Content: {$content}\n\nInstruction: {$instruction}"
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            throw new \Exception('Failed to improve content: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('AI Content Improvement Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate relevant hashtags for content.
     */
    public function generateHashtags(string $content, int $count = 5): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint, [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a social media expert. Generate {$count} relevant hashtags for the given content. Return only the hashtags, one per line, without the # symbol."
                    ],
                    [
                        'role' => 'user',
                        'content' => $content
                    ],
                ],
                'temperature' => 0.8,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $hashtagsText = $response->json('choices.0.message.content');
                $hashtags = array_map(
                    fn($tag) => '#' . trim($tag),
                    array_filter(explode("\n", $hashtagsText))
                );
                return array_slice($hashtags, 0, $count);
            }

            throw new \Exception('Failed to generate hashtags: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('AI Hashtag Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build system prompt based on parameters.
     */
    private function buildSystemPrompt(string $tone, string $length, array $platforms): string
    {
        $prompt = "You are a professional social media content writer. ";
        $prompt .= "Write in a {$tone} tone. ";

        $lengthGuide = match($length) {
            'short' => 'Keep it concise and under 100 words.',
            'medium' => 'Write a moderate length post around 100-200 words.',
            'long' => 'Write a detailed post up to 500 words.',
            default => 'Write a moderate length post.',
        };
        $prompt .= $lengthGuide;

        if (!empty($platforms)) {
            $platformList = implode(', ', $platforms);
            $prompt .= " This content will be posted on: {$platformList}. ";
            $prompt .= "Consider the best practices for these platforms.";
        }

        return $prompt;
    }

    /**
     * Get maximum tokens based on length.
     */
    private function getMaxTokens(string $length): int
    {
        return match($length) {
            'short' => 150,
            'medium' => 500,
            'long' => 1500,
            default => 500,
        };
    }
}
