<?php

/*
 * Laravel Blog Test
 * by Thomas
 * API Comments Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Retrieve all comments for a specific post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the Post.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $id)
    {
        // Find post robustly, using withoutGlobalScopes to bypass any potential filters
        $post = Post::withoutGlobalScopes()->findOrFail($id);

        // Retrieve and return all comments for that post, loading the author
        $comments = $post
            ->comments()
            ->with('author')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $comments
        ], 200);
    }

    /**
     * Retrieve all comments created by a specific user ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId The ID of the User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByUserId(Request $request, $userId)
    {
        // Fetch comments matching the provided user ID, loading author and post relationships
        $comments = Comment::where('user_id', $userId)
            ->with(['author', 'post'])
            ->latest()
            ->get();

        // Return 404 if no comments are found for the user
        if ($comments->isEmpty()) {
            return response()->json([
                'message' => 'No comments found for user ID ' . $userId
            ], 404);
        }

        return response()->json([
            'message' => 'Comments retrieved successfully for user ID ' . $userId,
            'data' => $comments
        ], 200);
    }

    /**
     * Store a newly created comment for a specific post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the Post to comment on.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id)
    {
        try {
            // Validate request
            $data = $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            // Look up post robustly
            $post = Post::withoutGlobalScopes()->findOrFail($id);

            // Assign authenticated user ID
            $data['user_id'] = $request->user()->id;

            // Create comment via the relationship
            $comment = $post->comments()->create($data);

            // Return comment with author relationship
            return response()->json([
                'message' => 'Comment created successfully',
                'data' => $comment->load('author')
            ], 201);
        } catch (ModelNotFoundException $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'message' => 'This Post does not exist '
                ], 404)
            );
        }
    }
}
