<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — ClientHub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
              :root {
            --bg: #f6f7fb;
            --surface: #ffffff;
            --text: #1e1b4b;
            --muted: #6b7280;
            --border: #e4e4f0;
            --primary: #4f46e5;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .auth-brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
        }

        .auth-brand h1 {
            margin: 0;
            font-size: 20px;
        }

        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
        }

        .auth-heading {
            margin: 0 0 6px;
            font-size: 22px;
        }

        .auth-subtext {
            margin: 0 0 26px;
            color: var(--muted);
            font-size: 14px;
        }

        .auth-error {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: var(--danger-bg);
            color: var(--danger-text);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            background: var(--surface);
        }

        .form-group input:focus {
            outline: none;
            border-color: #94a3b8;
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.08);
        }

        .field-error {
            margin: 6px 0 0;
            font-size: 12px;
            color: var(--danger-text);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .remember-me input {
            width: 15px;
            height: 15px;
            accent-color: var(--primary);
        }

        .form-row a {
            color: var(--text);
            font-weight: 600;
            text-decoration: none;
        }

        .form-row a:hover {
            text-decoration: underline;
        }

        .auth-submit {
            width: 100%;
            padding: 12px 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .auth-submit:hover {
            background: #111827;
        }

        .auth-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: var(--muted);
        }

        .auth-footer a {
            color: var(--text);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">

        <div class="auth-brand">
            <span class="auth-brand-mark">CH</span>
            <h1>ClientHub</h1>
        </div>

            <div class="auth-card">
            @if (session('success'))
                <div class="auth-error" style="background: #dcfce7; color: #166534;">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-error">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>

                    <!-- <a href="#">Forgot password?</a> -->
                </div>

                <button type="submit" class="auth-submit">Sign In</button>

            </form>

        </div>

        <p class="auth-footer">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
        </p>

    </div>

</body>
</html>