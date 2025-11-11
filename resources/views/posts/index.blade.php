@extends('layouts.app')
@section('content')
<h1>Posts</h1>
<form method="GET" action="{{ route('posts.index') }}" class="mb-4 d-flex flex-wrap align-items-center">
    <input type="text" name="search" class="form-control me-2 mb-2" placeholder="Search posts..."
        value="{{ request('search') }}" style="max-width: 250px;">

    <input type="text" name="tag" class="form-control me-2 mb-2" placeholder="Tag"
        value="{{ request('tag') }}" style="max-width: 200px;">

    <select name="category" class="form-select me-2 mb-2" style="max-width: 200px;">
        <option value="">All Categories</option>
        @foreach(App\Models\Category::all() as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="author" class="form-select me-2 mb-2" style="max-width: 200px;">
        <option value="">All Authors</option>
        @foreach(App\Models\User::all() as $user)
            <option value="{{ $user->id }}" @selected(request('author') == $user->id)>
                {{ $user->name }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-outline-primary mb-2">Filter</button>
</form>

@foreach($posts as $post)
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h5>
      <h6 class="card-subtitle mb-2 text-muted">By {{ $post->user->name }} in {{ $post->category?->name }}</h6>
      <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
	  <div class="post-buttons">
      <a href="{{ route('posts.show', $post) }}" class="btn btn-primary read-button">Read <i class="bi bi-arrow-right"></i></a>
		  @can('update', $post)
			<a href="{{ route('posts.edit', $post) }}" class="btn btn-info edit-button"><i class="bi bi-pencil-square"></i> Edit</a>
		  @endcan
		  @can('delete', $post)
			<form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-danger delete-button"><i class="bi bi-x-circle"></i> Delete</button></form>
		  @endcan
	  </div>
    </div>
  </div>
@endforeach
<div class="d-flex flex-column align-items-center mt-4">

    {{-- Showing X to Y of Z results --}}
    <div class="text-muted mb-2">
        Showing <strong>{{ $posts->firstItem() }}</strong> to <strong>{{ $posts->lastItem() }}</strong>
        of <strong>{{ $posts->total() }}</strong> results
    </div>

    {{-- Pagination links --}}
    <div class="bottom-pagination">
        {{ $posts->withQueryString()->onEachSide(1)->links() }}
    </div>

</div>

@endsection
