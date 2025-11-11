@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Categories</h1>

    @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Add Category</a>
    @endif

    <ul class="list-group">
        @foreach($categories as $category)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('posts.store','category=') }}{{$category->id}}"> {{ $category->name }}</a>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <span>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
