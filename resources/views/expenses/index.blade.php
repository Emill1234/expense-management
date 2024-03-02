@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/expensesindex.js') }}"></script>
    <div class="d-flex justify-content-between align-items-center">
        <h2>Expense List</h2>
        <div class="d-flex">
            <button class="btn btn-primary mr-2" id="toggleFilterBtn" onclick="toggleFilter()">Filter</button>
            <button class="btn btn-success" id="exportBtn" onclick="toggleExport()">Export</button>
        </div>
    </div>

    <form action="{{ route('expenses.filter') }}" method="GET" id="filterForm" style="display: none;">
        <div class="mb-3">
            <label for="filterCategory">Filter by Category:</label>
            <select class="form-control" id="filterCategory" name="filterCategory">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (request('filterCategory') == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <div class="mb-3">
            <label for="filterAmountFrom">Filter by Amount (From):</label>
            <input type="number" class="form-control" id="filterAmountFrom" name="filterAmountFrom" step="1" min="0" placeholder="From" value="{{ request('filterAmountFrom') }}">
        </div>
    
        <div class="mb-3">
            <label for="filterAmountTo">Filter by Amount (To):</label>
            <input type="number" class="form-control" id="filterAmountTo" name="filterAmountTo" step="1" min="0" placeholder="To" value="{{ request('filterAmountTo') }}">
        </div>
    
        <div class="mb-3">
            <button class="btn btn-primary" type="submit">Apply Filters</button>
            <button class="btn btn-secondary ml-2" type="button" onclick="clearFilters()">Clear Filters</button>
        </div>    
    </form>    

    <form action="{{ route('expenses.exportTable') }}" method="GET" id="exportForm" style="display: none;">
        <label for="exportFormat">Export Table Format:</label>
        <select class="form-control mb-3" id="exportFormat" name="exportFormat">
            <option value="csv">CSV</option>
            <option value="xlsx">XLSX</option>
            <option value="xml">XML</option>
            <option value="docx">DOCX</option>
        </select>
        <input type="hidden" name="filterCategory" value="{{ request('filterCategory') }}">
        <input type="hidden" name="filterAmountFrom" value="{{ request('filterAmountFrom') }}">
        <input type="hidden" name="filterAmountTo" value="{{ request('filterAmountTo') }}">
        <button class="btn btn-primary mb-3" type="submit">Export</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp    
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->category->name }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @php
                    $total += $expense->amount;
                @endphp    
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td>Total</td>
                <td></td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>    


    <a href="{{ route('expenses.create') }}" class="btn btn-success mt-3">Add Expense</a>
@endsection