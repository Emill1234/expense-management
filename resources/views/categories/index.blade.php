@extends('layouts.app')

@section('content')
    <h2>Categories</h2>
    <ul>
        @foreach($categories as $category)
            <li>{{ $category->name }}</li>
        @endforeach
    </ul>
    <a href="{{ route('categories.create') }}">Add Category</a>
@endsection