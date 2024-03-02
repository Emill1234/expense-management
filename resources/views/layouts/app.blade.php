<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layoutsapp.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>  
    <script src="{{ asset('js/layoutsapp.js') }}"></script>  
    <title>Expense Management</title>
</head>
<body>

    <header>
        <h1>Expense Management</h1>
    </header>

    <div id="sidebar">
        <div class="close-btn" onclick="toggleNav()">&#9776;</div>
        <a href="{{ route('expenses.index') }}">Expenses</a>
        <a href="{{ route('expenses.create') }}">Add Expense</a>
        <a href="{{ route('categories.index') }}">Categories</a>
        <a href="{{ route('categories.create') }}">Add Category</a>
    </div>

    <div class="container">
        <i id="sidebar-toggle" class="fas fa-bars" onclick="toggleNav()"></i>
        @yield('content')
    </div>

</body>
</html>