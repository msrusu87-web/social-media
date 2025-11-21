<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIContentController extends Controller
{
    protected $aiService;

    public function __construct(AIContentService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate AI content for a post.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:500',
            'tone' => 'nullable|in:professional,casual,friendly,formal',
            'length' => 'nullable|in:short,medium,long',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $content = $this->aiService->generateContent(
                $request->prompt,
                $request->tone ?? 'casual',
                $request->length ?? 'medium'
            );

            return response()->json([
                'success' => true,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate hashtags for content.
     */
    public function generateHashtags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'count' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hashtags = $this->aiService->generateHashtags(
                $request->content,
                $request->count ?? 5
            );

            return response()->json([
                'success' => true,
                'hashtags' => $hashtags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate hashtags: ' . $e->getMessage()
            ], 500);
        }
    }
}
