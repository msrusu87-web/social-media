<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Repost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepostController extends Controller
{
    /**
     * Store a newly created repost.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if already reposted
        $existingRepost = Repost::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingRepost) {
            return back()->with('error', 'You have already reposted this');
        }

        // Create repost
        Repost::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'comment' => $validated['comment'] ?? null,
        ]);

        // Increment reposts count
        $post->increment('reposts_count');

        return back()->with('success', 'Reposted successfully');
    }

    /**
     * Remove the specified repost.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $repost = Repost::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if (!$repost) {
            return back()->with('error', 'Repost not found');
        }

        $repost->delete();

        // Decrement reposts count
        $post->decrement('reposts_count');

        return back()->with('success', 'Repost removed successfully');
    }
}
