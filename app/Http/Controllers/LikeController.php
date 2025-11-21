<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Store a newly created like.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'likeable_type' => 'required|string|in:App\Models\Post,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        // Check if already liked
        $existingLike = Like::where('user_id', Auth::id())
            ->where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->first();

        if ($existingLike) {
            return back()->with('error', 'You have already liked this');
        }

        // Create like
        Like::create([
            'user_id' => Auth::id(),
            'likeable_type' => $validated['likeable_type'],
            'likeable_id' => $validated['likeable_id'],
        ]);

        // Increment likes count
        $likeable = $validated['likeable_type']::find($validated['likeable_id']);
        if ($likeable) {
            $likeable->increment('likes_count');
        }

        return back()->with('success', 'Liked successfully');
    }

    /**
     * Remove the specified like.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'likeable_type' => 'required|string|in:App\Models\Post,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        $like = Like::where('user_id', Auth::id())
            ->where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->first();

        if (!$like) {
            return back()->with('error', 'Like not found');
        }

        $like->delete();

        // Decrement likes count
        $likeable = $validated['likeable_type']::find($validated['likeable_id']);
        if ($likeable) {
            $likeable->decrement('likes_count');
        }

        return back()->with('success', 'Like removed successfully');
    }
}
