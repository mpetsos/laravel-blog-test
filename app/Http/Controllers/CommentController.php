<?php
namespace App\Http\Controllers;
use App\Models\Comment; use App\Models\Post; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class CommentController extends Controller
{
    public function store(Request $request, Post $post) { $data = $request->validate(['content'=>'required']); $data['user_id'] = Auth::id(); $post->comments()->create($data); return redirect()->back(); }
    public function update(Request $request, Comment $comment) { $this->authorize('update',$comment); $data = $request->validate(['content'=>'required']); $comment->update($data); return redirect()->back(); }
    public function destroy(Comment $comment) { $this->authorize('delete',$comment); $comment->delete(); return redirect()->back(); }
}
