<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepostController extends Controller
{
    /**
     * Repost a post.
     */
    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if already reposted
        $existingRepost = $request->user()->reposts()
            ->where('post_id', $post->id)
            ->first();

        if ($existingRepost) {
            return back()->with('error', 'You have already reposted this');
        }

        $request->user()->reposts()->create([
            'post_id' => $post->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Reposted successfully');
    }

    /**
     * Remove a repost.
     */
    public function destroy(Request $request, Post $post)
    {
        $repost = $request->user()->reposts()
            ->where('post_id', $post->id)
            ->first();

        if (!$repost) {
            return back()->with('error', 'Repost not found');
        }

        $repost->delete();

        return back()->with('success', 'Repost removed successfully');
    }
}
