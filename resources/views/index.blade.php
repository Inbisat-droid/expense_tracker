<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="page-wrapper">

<div class="main-card">

    <div class="topbar">
        <span>Welcome, <strong>{{ auth()->user()->name }}</strong></span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <h1>💰 Expense Tracker</h1>

    <div class="summary">
        <div class="card balance">
            <h3>Balance</h3>
            <h2 id="balance">₹0</h2>
        </div>
        <div class="card income">
            <h3>Income</h3>
            <h2 id="income">₹0</h2>
        </div>
        <div class="card expense">
            <h3>Expense</h3>
            <h2 id="expense">₹0</h2>
        </div>
    </div>

    <form id="transactionForm">

        <input type="text" id="desc" placeholder="Description" required>

        <input type="number" id="amount" placeholder="Amount" step="0.01" required>

        <select id="type" required>
            <option value="">Select Type</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
        </select>

        <select id="category" required>
            <option value="">Select Category</option>
            <option>Food</option>
            <option>Travel</option>
            <option>Shopping</option>
            <option>Salary</option>
            <option>Bills</option>
            <option>Entertainment</option>
            <option>Other</option>
        </select>

        <input type="date" id="date" required>

         <div class="attachment-group">
            <label for="attachment">Attachment (optional)</label>
            <input type="file" id="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.csv">
        </div>


        <br><button type="submit">Add Transaction</button>
        <button type="button" onclick="deleteAllTransactions()">Delete All</button>

    </form>

    <div class="filter">
        <label>Filter by Category:</label>
        <select id="filterCategory">
            <option value="All">All</option>
            <option>Food</option>
            <option>Travel</option>
            <option>Shopping</option>
            <option>Salary</option>
            <option>Bills</option>
            <option>Entertainment</option>
            <option>Other</option>
        </select>
    </div>

    <h2>Transaction History</h2>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Category</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Attachment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="list"></tbody>
    </table>

</div>

<script>
    window.initialTransactions = @json($transactions);
</script>
<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>