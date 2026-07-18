<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-body">

<div class="auth-card">

    <div class="auth-header">
        <h1>💰 Expense Tracker</h1>
        <p>Login to your account</p>
    </div>

    <div class="auth-content">

        @if (session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>Gmail / Email</label>
                <input type="email" name="email"
                    placeholder="Enter your email"
                    value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                    placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">Login</button>
            </div>

        </form>

    </div>

    <div class="auth-footer">
        New user? <a href="{{ route('register') }}">Register here</a>
    </div>

</div>

</body>
</html>