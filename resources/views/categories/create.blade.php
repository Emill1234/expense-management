@extends('layouts.app')

@section('content')
    <h2>Add Category</h2>
    <form action="{{ route('categories.store') }}" method="post">
        @csrf
        <label for="name">Category Name:</label>
        <input type="text" name="name" id="name" required>
        
        <button type="submit">Add Category</button>
    </form>
    <a href="{{ route('categories.index') }}">Back to Categories</a>
@endsection