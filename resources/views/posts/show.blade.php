@extends('layouts.app')
@section('content')
<div class="row">
  <div class="col-md-8">
    <h1>{{ $post->title }}</h1>
    <p class="text-muted">By {{ $post->user->name }} in {{ $post->category?->name }} | {{ $post->created_at->diffForHumans() }}</p>
    <div class="mb-3">{!! nl2br(e($post->content)) !!}</div>
    <p>Tags:
      @foreach($post->tags as $tag)
        <span class="badge bg-secondary">{{ $tag->name }}</span>
      @endforeach
    </p>
    <hr>
    <h4>Comments</h4>
    @auth
      <form method="POST" action="{{ route('comments.store', $post) }}">
        @csrf
        <div class="mb-3">
          <textarea name="content" class="form-control" rows="3" required></textarea>
        </div>
        <button class="btn btn-primary">Add Comment</button>
      </form>
    @else
      <p><a href="{{ route('login') }}">Login</a> to add a comment.</p>
    @endauth
    <div class="mt-3">
      @foreach($post->comments as $comment)
        <div class="border rounded p-2 mb-2">
          <strong>{{ $comment->user->name }}</strong> <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
          <p class="mb-1">{{ $comment->content }}</p>
          @can('update', $comment)
            <form method="POST" action="{{ route('comments.update', $comment) }}" class="d-inline">
              @csrf @method('PUT')
              <input type="text" name="content" class="form-control d-inline w-50" value="{{ $comment->content }}">
              <button class="btn btn-sm btn-secondary mt-1">Update</button>
            </form>
          @endcan
          @can('delete', $comment)
            <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          @endcan
        </div>
      @endforeach
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5>About author</h5>
        <p>{{ $post->user->name }}</p>
      </div>
    </div>
  </div>
</div>
@endsection
