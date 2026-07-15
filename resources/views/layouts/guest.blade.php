<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Johen Gaming'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0e1a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 12s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: rgba(9,135,245,0.15);
            top: -15%; right: -10%;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: rgba(99,102,241,0.12);
            bottom: -20%; left: -8%;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: rgba(236,72,153,0.08);
            top: 40%; left: 50%;
            animation-delay: -8s;
        }
        @keyframes orbFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            25% { transform: translate(30px,-40px) scale(1.05); }
            50% { transform: translate(-20px,20px) scale(0.95); }
            75% { transform: translate(40px,30px) scale(1.02); }
        }

        .grid-lines {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none; z-index: 0;
        }

        .auth-page {
            position: relative; z-index: 1;
            width: 100%; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }

        .auth-container {
            display: flex;
            width: 100%;
            max-width: 1040px;
            min-height: 600px;
            background: rgba(13,18,34,0.6);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 30px 80px -20px rgba(0,0,0,0.6),
                0 0 80px -20px rgba(9,135,245,0.08);
            animation: containerIn 0.6s ease-out;
        }
        @keyframes containerIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-brand-panel {
            display: none;
            flex: 1;
            background:
                radial-gradient(ellipse at 30% 20%, rgba(9,135,245,0.12), transparent 60%),
                radial-gradient(ellipse at 70% 80%, rgba(99,102,241,0.08), transparent 50%),
                linear-gradient(160deg, #0d1222, #111827);
            padding: 3rem 2.5rem;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-brand-panel::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                repeating-linear-gradient(105deg, rgba(255,255,255,0.02) 0 1px, transparent 1px 40px);
            pointer-events: none;
        }
        .auth-brand-top .brand-logo {
            display: inline-flex; align-items: center; gap: 0.6rem;
            font-family: 'Sora', sans-serif;
            font-weight: 800; font-size: 1.1rem; color: #fff;
            text-decoration: none;
        }
        .brand-logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #0987F5, #6366f1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff;
            box-shadow: 0 4px 12px -4px rgba(9,135,245,0.4);
        }
        .brand-highlight { color: #0987F5; }

        .brand-testimonial {
            margin-top: auto;
        }
        .brand-testimonial blockquote {
            font-size: 1.05rem; font-weight: 500; line-height: 1.6;
            color: #cbd5e1;
            max-width: 360px;
            font-style: italic;
        }
        .brand-testimonial .author {
            display: flex; align-items: center; gap: 0.7rem;
            margin-top: 1.2rem;
        }
        .brand-testimonial .author-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0987F5, #6366f1);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.8rem; color: #fff;
        }
        .brand-testimonial .author-name { font-weight: 600; font-size: 0.85rem; }
        .brand-testimonial .author-title { font-size: 0.75rem; color: #64748b; }

        .brand-stats {
            display: flex; gap: 2rem;
        }
        .brand-stat-item .stat-value {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem; font-weight: 800;
            background: linear-gradient(135deg, #0987F5, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-stat-item .stat-label {
            font-size: 0.75rem; color: #64748b;
            margin-top: 0.15rem;
        }

        .auth-form-panel {
            flex: 1;
            padding: 2.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 480px;
        }

        .auth-header {
            animation: fadeUp 0.5s ease-out 0.15s both;
        }
        .auth-header h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem; font-weight: 800;
            margin-bottom: 0.3rem;
        }
        .auth-header p {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 1.8rem;
        }

        .auth-form {
            animation: fadeUp 0.5s ease-out 0.3s both;
        }

        .form-group {
            margin-bottom: 1.1rem;
            position: relative;
            animation: fadeUp 0.4s ease-out both;
        }
        .form-group:nth-child(1) { animation-delay: 0.25s; }
        .form-group:nth-child(2) { animation-delay: 0.3s; }
        .form-group:nth-child(3) { animation-delay: 0.35s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.45s; }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap .input-icon {
            position: absolute;
            left: 0.85rem; top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            color: #475569;
            transition: color 0.2s;
            pointer-events: none;
        }
        .input-wrap.focused .input-icon { color: #0987F5; }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.75rem 0.9rem 0.75rem 2.6rem;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-group input::placeholder { color: #334155; }
        .form-group input:hover { border-color: rgba(255,255,255,0.14); }
        .form-group input:focus {
            border-color: #0987F5;
            background: rgba(9,135,245,0.04);
            box-shadow: 0 0 0 3px rgba(9,135,245,0.1), inset 0 0 0 1px rgba(9,135,245,0.15);
        }
        .form-group input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        .toggle-pass {
            position: absolute;
            right: 0.75rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #475569;
            cursor: pointer;
            padding: 0.35rem;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: #94a3b8; }

        .form-group .error-text {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.3rem;
            display: flex; align-items: center; gap: 0.3rem;
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
            text-transform: none;
            letter-spacing: 0;
        }
        .form-check input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #0987F5;
            cursor: pointer;
        }
        .form-check a {
            color: #0987F5;
            font-size: 0.82rem;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .form-check a:hover { opacity: 0.8; text-decoration: underline; }

        .btn-primary {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #0987F5, #6366f1);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(9,135,245,0.4);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-primary .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }
        .btn-primary.loading .spinner { display: block; }
        .btn-primary.loading .btn-text { opacity: 0.7; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-footer-text {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: #64748b;
            animation: fadeUp 0.4s ease-out 0.5s both;
        }
        .auth-footer-text a {
            color: #0987F5;
            text-decoration: none;
            font-weight: 700;
            transition: opacity 0.2s;
        }
        .auth-footer-text a:hover { opacity: 0.8; text-decoration: underline; }

        .alert {
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: fadeUp 0.3s ease-out;
        }
        .alert-success {
            background: rgba(52,211,153,0.08);
            border: 1px solid rgba(52,211,153,0.15);
            color: #34d399;
        }
        .alert-error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            color: #f87171;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (min-width: 820px) {
            .auth-brand-panel { display: flex; }
        }
        @media (max-width: 819px) {
            .auth-container { max-width: 480px; }
            .auth-form-panel { max-width: 100%; padding: 2rem 1.8rem; }
        }
        @media (max-width: 480px) {
            .auth-page { padding: 0.8rem; }
            .auth-container { border-radius: 16px; }
            .auth-form-panel { padding: 1.5rem; }
            .auth-header h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-lines"></div>

    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-brand-panel">
                <div class="auth-brand-top">
                    <a href="{{ route('home') }}" class="brand-logo">
                        <div class="brand-logo-icon"><i class="fas fa-gamepad"></i></div>
                        JOHEN<span class="brand-highlight">GAMING</span>
                    </a>
                </div>

                <div class="brand-testimonial">
                    <blockquote>"Top up di Johen Gaming itu cepet banget, 5 detik langsung masuk. Udah langganan 2 tahun!"</blockquote>
                    <div class="author">
                        <div class="author-avatar">R</div>
                        <div>
                            <div class="author-name">Rizky Pratama</div>
                            <div class="author-title">Top 10 Top Up 2025</div>
                        </div>
                    </div>
                </div>

                <div class="brand-stats">
                    <div class="brand-stat-item">
                        <div class="stat-value">50K+</div>
                        <div class="stat-label">Transaksi Sukses</div>
                    </div>
                    <div class="brand-stat-item">
                        <div class="stat-value">4.9</div>
                        <div class="stat-label">Rating Pengguna</div>
                    </div>
                    <div class="brand-stat-item">
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Layanan Aktif</div>
                    </div>
                </div>
            </div>

            <div class="auth-form-panel">
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
        </div>
    </div>

    @stack('scripts')
</body>
</html>
