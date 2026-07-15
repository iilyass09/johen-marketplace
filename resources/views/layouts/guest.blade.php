<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Johen Gaming') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #021B31;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(9,135,245,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(9,135,245,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-brand a {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 800;
        }
        .auth-brand .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0987F5, #6366f1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .auth-brand .brand-highlight { color: #0987F5; }
        .auth-card {
            background: #032D52;
            border: 1px solid rgba(9,135,245,0.15);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .auth-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .auth-card .subtitle {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.9rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-group input:focus {
            border-color: #0987F5;
            box-shadow: 0 0 0 3px rgba(9,135,245,0.15);
        }
        .form-group input::placeholder {
            color: #475569;
        }
        .form-group .error-text {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 0.3rem;
        }
        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1rem 0;
        }
        .form-check label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: #94a3b8;
            cursor: pointer;
        }
        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #0987F5;
            cursor: pointer;
        }
        .form-check a {
            color: #0987F5;
            font-size: 0.82rem;
            text-decoration: none;
            font-weight: 500;
        }
        .form-check a:hover { text-decoration: underline; }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #0987F5, #6366f1);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(9,135,245,0.35);
        }
        .btn-primary:active { transform: translateY(0); }
        .auth-footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .auth-footer a {
            color: #0987F5;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover { text-decoration: underline; }
        .alert {
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-success {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.2);
            color: #34d399;
        }
        .alert-error {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.2);
            color: #f87171;
        }
        @media (max-width: 480px) {
            .auth-wrapper { padding: 1rem; }
            .auth-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-brand">
            <a href="/">
                <div class="brand-icon"><i class="fas fa-gamepad"></i></div>
                <span>JOHEN<span class="brand-highlight">GAMING</span></span>
            </a>
        </div>

        <div class="auth-card">
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{ $slot }}
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} Johen Gaming
        </div>
    </div>
</body>
</html>
