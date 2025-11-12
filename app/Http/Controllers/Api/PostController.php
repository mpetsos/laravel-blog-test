<?php

/*
 * Laravel Blog Test
 * by Thomas
 * API Posts Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    // List posts with filters
    public function index(Request $request)
    {
        $query = Post::with(['author:id,name,email', 'tags:id,name', 'category:id,name']);

        if ($request->author) {
            $query->where('user_id', $request->author);
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->tags) {
            $tags = explode(',', $request->tags);
            $query->whereHas('tags', fn($q) => $q->whereIn('id', $tags));
        }

        return response()->json($query->get(), 200);
    }

    // Create post
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        // Check for duplicate title
        $existingPost = Post::where('title', $request->title)
            ->first();

        if ($existingPost) {
            return response()->json([
                'status' => 'error',
                'message' => 'A post with this title already exists. Please choose a different title.'
            ], 422);
        } else {
            $data['user_id'] = $request->user()->id;
            $data['slug'] = \Str::slug($data['title']);
            $post = Post::create($data);

            // Attach default "new" tag
            $newTag = Tag::firstOrCreate(['slug' => 'new'], ['name' => 'new']);
            $post->tags()->syncWithoutDetaching([$newTag->id]);

            if ($request->tags) {
                $post->tags()->syncWithoutDetaching($request->tags);
            }

            return response()->json($post->load(['tags', 'author', 'category']), 201);
        }
    }

    // Show single post
    public function show($id, $slug)
    {
        try {
            $post = Post::with(['tags', 'author', 'category'])
                ->where('id', $id)
                ->where('slug', $slug)
                ->firstOrFail();
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'message' => 'This Post does not exist '
                ], 404)
            );
        }
    }

    // Update post
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            if ($post->user_id !== $request->user()->id) {
                // Return a 403 Forbidden response if the user is not the author
                return response()->json([
                    'message' => 'You are not authorized to update this post.'
                ], 403);
            }

            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'category_id' => 'sometimes|exists:categories,id',
                'tags' => 'sometimes|array|exists:tags,id',
            ]);
            // Check for duplicate title
            $existingPost = Post::where('title', $request->title)
                ->first();
            if ($existingPost) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A post with this title already exists. Please choose a different title.'
                ], 422);
            } else {
                if (isset($data['title'])) {
                    $data['slug'] = \Str::slug($data['title']);
                }

                $post->update($data);

                // Attach "edited" tag
                $editedTag = Tag::firstOrCreate(['slug' => 'edited'], ['name' => 'edited']);
                $post->tags()->syncWithoutDetaching([$editedTag->id]);

                if ($request->tags) {
                    $post->tags()->syncWithoutDetaching($request->tags);
                }

                return response()->json($post->load(['tags', 'author', 'category']), 200);
            }
        } catch (ModelNotFoundException $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'message' => 'This Post does not exist '
                ], 404)
            );
        }
    }

    // Delete post
    public function destroy(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            // Authorization Check:
            // Ensure the authenticated user (author) is the one trying to delete it.
            if ($post->user_id !== $request->user()->id) {
                // Return a 403 Forbidden response if the user is not the author
                return response()->json([
                    'message' => 'You are not authorized to delete this post.'
                ], 403);
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully'], 200);
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
