<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIContentController extends Controller
{
    public function __construct(
        protected AIContentService $aiContentService
    ) {}

    /**
     * Generate AI content
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string',
            'platform' => 'nullable|string',
            'tone' => 'nullable|string',
        ]);

        $content = $this->aiContentService->generateContent(
            $request->input('prompt'),
            $request->input('platform'),
            $request->input('tone')
        );

        return response()->json([
            'success' => true,
            'content' => $content,
        ]);
    }

    /**
     * Enhance existing content
     */
    public function enhance(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'enhancement_type' => 'required|string',
        ]);

        $enhanced = $this->aiContentService->enhanceContent(
            $request->input('content'),
            $request->input('enhancement_type')
        );

        return response()->json([
            'success' => true,
            'content' => $enhanced,
        ]);
    }

    /**
     * Generate hashtags
     */
    public function hashtags(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $hashtags = $this->aiContentService->generateHashtags(
            $request->input('content')
        );

        return response()->json([
            'success' => true,
            'hashtags' => $hashtags,
        ]);
    }
}
