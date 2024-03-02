@extends('layouts.app')

@section('content')
    <h2>Add Expense</h2>
    <form action="{{ route('expenses.store') }}" method="post">
        @csrf
        <label for="description">Description:</label>
        <input type="text" name="description" id="description" required>
        
        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" step="0.01" required>
        
        <label for="category">Category:</label>
        <select name="category_id" id="category" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        
        <button type="submit">Add Expense</button>
    </form>
    <a href="{{ route('expenses.index') }}">Back to Expense List</a>
@endsection