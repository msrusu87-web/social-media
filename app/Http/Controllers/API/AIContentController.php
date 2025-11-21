<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIContentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIContentController extends Controller
{
    public function __construct(
        private AIContentService $aiContentService
    ) {}

    /**
     * Generate AI content based on prompt.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'tone' => 'nullable|string|in:professional,casual,friendly,formal',
            'length' => 'nullable|string|in:short,medium,long',
            'platforms' => 'nullable|array',
        ]);

        try {
            $content = $this->aiContentService->generateContent(
                $request->input('prompt'),
                $request->input('tone', 'professional'),
                $request->input('length', 'medium'),
                $request->input('platforms', [])
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
     * Improve existing content with AI.
     */
    public function improve(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'instruction' => 'nullable|string|max:200',
        ]);

        try {
            $improved = $this->aiContentService->improveContent(
                $request->input('content'),
                $request->input('instruction', 'Improve this content')
            );

            return response()->json([
                'success' => true,
                'content' => $improved,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to improve content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate hashtags for content.
     */
    public function generateHashtags(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $hashtags = $this->aiContentService->generateHashtags(
                $request->input('content'),
                $request->input('count', 5)
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
}
