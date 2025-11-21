<?php

namespace App\Http\Controllers\API;

use App\Enums\PlatformType;
use App\Http\Controllers\Controller;
use App\Models\SocialConnection;
use App\Services\SocialMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialConnectionController extends Controller
{
    public function __construct(
        protected SocialMediaService $socialMediaService
    ) {}

    /**
     * Get all social connections for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $connections = $request->user()
            ->socialConnections()
            ->get();

        return response()->json($connections);
    }

    /**
     * Connect a social media account
     */
    public function connect(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string',
            'access_token' => 'required|string',
            'refresh_token' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $connection = $request->user()->socialConnections()->create([
            'platform' => PlatformType::from($request->input('platform')),
            'access_token' => encrypt($request->input('access_token')),
            'refresh_token' => $request->input('refresh_token') 
                ? encrypt($request->input('refresh_token')) 
                : null,
            'expires_at' => $request->input('expires_at'),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'connection' => $connection,
        ], 201);
    }

    /**
     * Disconnect a social media account
     */
    public function disconnect(SocialConnection $connection): JsonResponse
    {
        $this->authorize('delete', $connection);

        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Connection disconnected successfully',
        ]);
    }

    /**
     * Refresh a social connection token
     */
    public function refresh(SocialConnection $connection): JsonResponse
    {
        $this->authorize('update', $connection);

        $refreshed = $this->socialMediaService->refreshToken($connection);

        return response()->json([
            'success' => true,
            'connection' => $refreshed,
        ]);
    }
}
