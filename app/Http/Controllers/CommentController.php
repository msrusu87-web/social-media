<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        $comment->load('user.profile');

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ], 201);
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }
}
