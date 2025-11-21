<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIContentController extends Controller
{
    protected AIContentService $aiService;

    public function __construct(AIContentService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate content using AI.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'platform' => 'required|string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
            'tone' => 'nullable|string|in:professional,casual,friendly,formal,humorous',
            'length' => 'nullable|string|in:short,medium,long',
        ]);

        try {
            $content = $this->aiService->generateContent(
                $request->prompt,
                $request->platform,
                $request->tone ?? 'casual',
                $request->length ?? 'medium'
            );

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate hashtags for content.
     */
    public function generateHashtags(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'count' => 'nullable|integer|min:1|max:30',
        ]);

        try {
            $hashtags = $this->aiService->generateHashtags(
                $request->content,
                $request->count ?? 5
            );

            return response()->json([
                'success' => true,
                'hashtags' => $hashtags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate hashtags: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Improve existing content.
     */
    public function improve(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'improvement_type' => 'required|string|in:grammar,engagement,clarity,brevity',
        ]);

        try {
            $improvedContent = $this->aiService->improveContent(
                $request->content,
                $request->improvement_type
            );

            return response()->json([
                'success' => true,
                'content' => $improvedContent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to improve content: ' . $e->getMessage(),
            ], 500);
        }
    }
}
