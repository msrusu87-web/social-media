<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Like a post or comment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:App\Models\Post,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        $likeableType = $request->likeable_type;
        $likeable = $likeableType::findOrFail($request->likeable_id);

        $request->user()->likes()->firstOrCreate([
            'likeable_type' => $likeableType,
            'likeable_id' => $request->likeable_id,
        ]);

        return back()->with('success', 'Liked successfully');
    }

    /**
     * Unlike a post or comment.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:App\Models\Post,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        $request->user()->likes()
            ->where('likeable_type', $request->likeable_type)
            ->where('likeable_id', $request->likeable_id)
            ->delete();

        return back()->with('success', 'Unliked successfully');
    }
}
