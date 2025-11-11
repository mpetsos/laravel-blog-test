@extends('layouts.app')
@section('content')
<h1>Edit Category</h1>
<form method="POST" action="{{ route('categories.update', $category) }}">
  @csrf @method('PUT')
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Parent</label>
	<select name="parent_id" class="form-control">
		<option value="">No parent</option>
		@foreach($parents as $parent)
			<option value="{{ $parent->id }}">{{ $parent->name }}</option>
		@endforeach
	</select>
  </div>
  <button class="btn btn-primary">Update</button>
</form>
@endsection
