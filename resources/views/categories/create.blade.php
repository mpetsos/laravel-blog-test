@extends('layouts.app')
@section('content')
<h1>Create Category</h1>
<form method="POST" action="{{ route('categories.store') }}">
  @csrf
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Parent</label>
    <select name="parent_id" class="form-select">
      <option value="">-- none --</option>
      @foreach($parents as $p)
        <option value="{{ $p->id }}">{{ $p->name }}</option>
      @endforeach
    </select>
  </div>
  <button class="btn btn-primary">Create</button>
</form>
@endsection
