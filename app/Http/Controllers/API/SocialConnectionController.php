<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialConnection;
use App\Services\SocialMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialConnectionController extends Controller
{
    protected SocialMediaService $socialService;

    public function __construct(SocialMediaService $socialService)
    {
        $this->socialService = $socialService;
    }

    /**
     * Display a listing of the user's social connections.
     */
    public function index(): JsonResponse
    {
        $connections = Auth::user()
            ->socialConnections()
            ->get();

        return response()->json($connections);
    }

    /**
     * Get OAuth URL for connecting a platform.
     */
    public function getAuthUrl(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
        ]);

        try {
            $url = $this->socialService->getAuthUrl($request->platform);

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate auth URL: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle OAuth callback and store connection.
     */
    public function callback(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
            'code' => 'required|string',
        ]);

        try {
            $connection = $this->socialService->handleCallback(
                $request->platform,
                $request->code,
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Platform connected successfully',
                'connection' => $connection,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect platform: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect a social platform.
     */
    public function disconnect(SocialConnection $connection): JsonResponse
    {
        $this->authorize('delete', $connection);

        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Platform disconnected successfully',
        ]);
    }

    /**
     * Refresh token for a social connection.
     */
    public function refresh(SocialConnection $connection): JsonResponse
    {
        $this->authorize('update', $connection);

        try {
            $updatedConnection = $this->socialService->refreshToken($connection);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'connection' => $updatedConnection,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage(),
            ], 500);
        }
    }
}
