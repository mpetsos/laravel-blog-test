<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Apply middleware.
     */
    public function __construct()
    {
        // Only guests can see index & show. Auth required for create/edit/delete.
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'tags', 'category']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($author = $request->input('author')) {
            $query->where('user_id', $author);
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        if ($tag = $request->input('tag')) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.name', 'like', "%{$tag}%");
            });
        }

        $posts = $query->latest()->paginate(5);  // 5 posts per page

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array|nullable',
            'tags.*' => 'exists:tags,id',  // ✅ validate IDs from select
        ], [
            'title.unique' => 'A post with this title already exists.',
        ]);

        // Create post
        $post = Auth::user()->posts()->create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'category_id' => $request->category_id,
        ]);

        // ✅ Collect all tag IDs
        $tagIds = $request->input('tags', []);

        // ✅ Add the "new" tag automatically
        $newTag = Tag::firstOrCreate(
            ['slug' => 'new'],
            ['name' => 'new']
        );

        // ✅ Merge and remove duplicates
        $tagIds[] = $newTag->id;
        $tagIds = array_unique($tagIds);

        // ✅ Sync all tags
        $post->tags()->sync($tagIds);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'category', 'tags', 'comments.user']);
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        // Ensure only the author can edit
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        // 🔒 Make sure only the author can edit
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // ✅ Validate input
        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'title')->ignore($post->id),
            ],
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array|nullable',
            'tags.*' => 'exists:tags,id',  // ✅ IDs from multi-select
        ], [
            'title.unique' => 'A post with this title already exists.',
        ]);

        // 🧠 Track original values
        $originalTitle = $post->title;
        $originalContent = $post->content;

        // 📝 Update post fields
        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'category_id' => $request->category_id,
        ]);

        // 🏷️ Collect tags from multi-select
        $tagIds = $request->input('tags', []);

        // 🕵️ Detect if title or content changed
        $titleChanged = $originalTitle !== $post->title;
        $contentChanged = $originalContent !== $post->content;

        if ($titleChanged || $contentChanged) {
            // ✅ Add "edited" tag automatically
            $editedTag = Tag::firstOrCreate(
                ['slug' => 'edited'],
                ['name' => 'edited']
            );
            $tagIds[] = $editedTag->id;

            // ✅ Keep "new" tag if already attached
            $newTag = Tag::firstOrCreate(
                ['slug' => 'new'],
                ['name' => 'new']
            );
            $tagIds[] = $newTag->id;
        }

        // ✅ Remove duplicates and sync tags
        $post->tags()->sync(array_unique($tagIds));

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
