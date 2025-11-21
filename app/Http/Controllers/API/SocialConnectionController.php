<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialConnection;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialConnectionController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    /**
     * Get all social connections for the authenticated user.
     */
    public function index(Request $request)
    {
        $connections = $request->user()->socialConnections;

        return response()->json([
            'success' => true,
            'connections' => $connections
        ]);
    }

    /**
     * Connect a social media account.
     */
    public function connect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:facebook,instagram,x,tiktok,youtube,pinterest',
            'access_token' => 'required|string',
            'refresh_token' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'platform_user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $connection = $request->user()->socialConnections()->updateOrCreate(
            ['platform' => $request->platform],
            [
                'platform_user_id' => $request->platform_user_id,
                'access_token' => $request->access_token,
                'refresh_token' => $request->refresh_token,
                'expires_at' => $request->expires_at,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'connection' => $connection
        ], 201);
    }

    /**
     * Disconnect a social media account.
     */
    public function disconnect(Request $request, $platform)
    {
        $connection = $request->user()->socialConnections()
            ->where('platform', $platform)
            ->first();

        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Connection not found'
            ], 404);
        }

        $connection->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Disconnected successfully'
        ]);
    }

    /**
     * Get OAuth URL for a platform.
     */
    public function getAuthUrl(Request $request, $platform)
    {
        try {
            $url = $this->socialMediaService->getAuthUrl($platform);

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
