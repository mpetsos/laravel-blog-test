@extends('layouts.app')
@section('content')
<h1>Create Post</h1>

<!-- Display success message if any -->
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('posts.store') }}">
  @csrf

  <!-- Title -->
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" 
           class="form-control @error('title') is-invalid @enderror" 
           value="{{ old('title') }}" required>
    @error('title')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <!-- Category -->
  <div class="mb-3">
    <label class="form-label">Category</label>
    <select name="category_id" 
            class="form-select @error('category_id') is-invalid @enderror">
      <option value="">-- none --</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
            @if($c->parent_id) {{'--'}}@endif {{ $c->name }}
        </option>
      @endforeach
    </select>
    @error('category_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Tags (ctrl/cmd+click to select multiple)</label>
    <select name="tags[]" class="form-select" multiple>
      @foreach($tags as $t)
        <option value="{{ $t->id }}" {{ in_array($t->id, old('tags', [])) ? 'selected' : '' }}>
            {{ $t->name }}
        </option>
      @endforeach
    </select>
  </div>

  <!-- Content -->
  <div class="mb-3">
    <label class="form-label">Content</label>
    <textarea name="content" 
              class="form-control @error('content') is-invalid @enderror" 
              rows="8" required>{{ old('content') }}</textarea>
    @error('content')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <button class="btn btn-primary">Create</button>
</form>
@endsection

