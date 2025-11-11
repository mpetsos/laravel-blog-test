<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;

class PostController extends Controller
{
    // 1. List posts with filters
    public function index(Request $request)
    {
        $query = Post::with(['author:id,name,email','tags:id,name','category:id,name']);

        if($request->author) { $query->where('user_id',$request->author); }
        if($request->category) { $query->where('category_id',$request->category); }
        if($request->tags) {
            $tags = explode(',',$request->tags);
            $query->whereHas('tags', fn($q) => $q->whereIn('id',$tags));
        }

        return response()->json($query->get(), 200);
    }

    // 2. Create post
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|string|max:255',
            'content'=>'required|string',
            'category_id'=>'required|exists:categories,id',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = \Str::slug($data['title']);

        $post = Post::create($data);

        // Attach default "new" tag
        $newTag = Tag::firstOrCreate(['slug'=>'new'], ['name'=>'new']);
        $post->tags()->syncWithoutDetaching([$newTag->id]);

        if($request->tags){
            $post->tags()->syncWithoutDetaching($request->tags);
        }

        return response()->json($post->load(['tags','author','category']),201);
    }

    // 3. Show single post
    public function show($id,$slug)
    {
        $post = Post::with(['tags','author','category'])
            ->where('id',$id)
            ->where('slug',$slug)
            ->firstOrFail();
        return response()->json($post,200);
    }

    // 4. Update post
    public function update(Request $request,$id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title'=>'sometimes|string|max:255',
            'content'=>'sometimes|string',
            'category_id'=>'sometimes|exists:categories,id',
            'tags'=>'sometimes|array|exists:tags,id',
        ]);

        if(isset($data['title'])){
            $data['slug'] = \Str::slug($data['title']);
        }

        $post->update($data);

        // Attach "edited" tag
        $editedTag = Tag::firstOrCreate(['slug'=>'edited'], ['name'=>'edited']);
        $post->tags()->syncWithoutDetaching([$editedTag->id]);

        if($request->tags){
            $post->tags()->syncWithoutDetaching($request->tags);
        }

        return response()->json($post->load(['tags','author','category']),200);
    }

    // 5. Delete post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message'=>'Post deleted'],200);
    }
}
