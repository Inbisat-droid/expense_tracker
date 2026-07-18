<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Expense Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-body">

<div class="auth-card">

    <div class="auth-header">
        <h1>💰 Expense Tracker</h1>
        <p>Create a new account</p>
    </div>

    <div class="auth-content">

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="name"
                    placeholder="Enter your username"
                    value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>Gmail / Email</label>
                <input type="email" name="email"
                    placeholder="Enter your email"
                    value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                    placeholder="Min 6 characters" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation"
                    placeholder="Re-enter password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">Create Account</button>
            </div>

        </form>

    </div>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Login here</a>
    </div>

</div>

</body>
</html>