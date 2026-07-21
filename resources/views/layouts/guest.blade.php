<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Johen Gaming'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
    <style>
        :root{
            --auth-bg:#100821;
            --auth-text:#f5f3fb;
            --auth-card-bg:rgba(30,17,54,.7);
            --auth-card-border:rgba(255,255,255,.06);
            --auth-label:#b3a6d6;
            --auth-muted:#7c6ea3;
            --auth-input-bg:rgba(255,255,255,.04);
            --auth-input-border:rgba(255,255,255,.07);
            --auth-input-placeholder:#3d2d5c;
            --auth-input-color:#f5f3fb;
            --auth-icon-color:#5a4a7a;
            --auth-grid-line:rgba(255,255,255,.018);
            --auth-card-shadow:0 0 0 1px rgba(255,255,255,.02), 0 30px 80px -20px rgba(0,0,0,.6), 0 0 80px -20px rgba(124,58,237,.08);
        }
        [data-theme="light"]{
            --auth-bg:#f0ecf7;
            --auth-text:#1a1a2e;
            --auth-card-bg:rgba(255,255,255,.85);
            --auth-card-border:rgba(0,0,0,.06);
            --auth-label:#6b5e87;
            --auth-muted:#7c6ea3;
            --auth-input-bg:rgba(0,0,0,.03);
            --auth-input-border:rgba(0,0,0,.08);
            --auth-input-placeholder:#a99ec2;
            --auth-input-color:#1a1a2e;
            --auth-icon-color:#a99ec2;
            --auth-grid-line:rgba(0,0,0,.04);
            --auth-card-shadow:0 0 0 1px rgba(0,0,0,.03), 0 10px 40px -12px rgba(0,0,0,.12);
        }

        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{
            font-family:'Inter',sans-serif;
            background:var(--auth-bg);
            color:var(--auth-text);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            overflow-x:hidden;
        }

        .auth-theme-btn{
            position:fixed;top:1.2rem;right:1.2rem;z-index:10;
            width:40px;height:40px;border-radius:12px;
            background:var(--auth-card-bg);
            border:1px solid var(--auth-card-border);
            backdrop-filter:blur(12px);
            color:var(--auth-muted);
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            transition:all .25s ease;
        }
        .auth-theme-btn:hover{color:var(--auth-text);transform:scale(1.05);}
        .auth-theme-btn svg{width:18px;height:18px;}
        .auth-theme-btn .icon-sun,
        .auth-theme-btn .icon-moon{display:none;}
        [data-theme="dark"] .auth-theme-btn .icon-moon,
        html:not([data-theme="light"]) .auth-theme-btn .icon-moon{display:block;}
        [data-theme="light"] .auth-theme-btn .icon-sun{display:block;}

        .auth-bg-glow{
            position:fixed;inset:0;pointer-events:none;z-index:0;
            overflow:hidden;
        }
        .auth-bg-glow .glow{
            position:absolute;border-radius:50%;filter:blur(100px);
            animation:glowFloat 14s ease-in-out infinite;
        }
        .auth-bg-glow .glow-1{
            width:550px;height:550px;
            background:rgba(124,58,237,.2);
            top:-20%;right:-10%;
        }
        .auth-bg-glow .glow-2{
            width:400px;height:400px;
            background:rgba(157,92,245,.12);
            bottom:-25%;left:-8%;
            animation-delay:-5s;
        }
        .auth-bg-glow .glow-3{
            width:300px;height:300px;
            background:rgba(124,58,237,.08);
            top:50%;left:60%;
            animation-delay:-10s;
        }
        [data-theme="light"] .auth-bg-glow .glow-1{background:rgba(124,58,237,.08);}
        [data-theme="light"] .auth-bg-glow .glow-2{background:rgba(157,92,245,.06);}
        [data-theme="light"] .auth-bg-glow .glow-3{background:rgba(124,58,237,.04);}
        @keyframes glowFloat{
            0%,100%{transform:translate(0,0) scale(1);}
            25%{transform:translate(40px,-50px) scale(1.08);}
            50%{transform:translate(-30px,30px) scale(.92);}
            75%{transform:translate(50px,40px) scale(1.05);}
        }

        .auth-grid{
            position:fixed;inset:0;pointer-events:none;z-index:0;
            background-image:
                linear-gradient(var(--auth-grid-line) 1px,transparent 1px),
                linear-gradient(90deg,var(--auth-grid-line) 1px,transparent 1px);
            background-size:64px 64px;
        }

        .auth-page{
            position:relative;z-index:1;
            width:100%;min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:1.5rem;
        }

        .auth-container{
            width:100%;max-width:600px;
            background:var(--auth-card-bg);
            backdrop-filter:blur(30px);
            border:1px solid var(--auth-card-border);
            border-radius:20px;
            padding:2rem 1.8rem;
            box-shadow:var(--auth-card-shadow);
            animation:containerIn .55s ease-out;
        }
        @keyframes containerIn{
            from{opacity:0;transform:translateY(24px) scale(.97);}
            to{opacity:1;transform:translateY(0) scale(1);}
        }

        .auth-logo{
            display:flex;align-items:center;justify-content:center;gap:.7rem;
            margin-bottom:1.25rem;
            text-decoration:none;
            animation:fadeUp .5s ease-out .1s both;
        }
        .auth-logo-img{
            width:42px;height:42px;border-radius:10px;
            object-fit:contain;
        }
        .auth-logo-text{
            font-family:'Sora',sans-serif;
            font-weight:800;font-size:1.15rem;color:var(--auth-text);
            letter-spacing:.03em;
        }
        .auth-logo-text span{color:#9d5cf5;}

        .auth-header{
            text-align:center;
            margin-bottom:1.1rem;
            animation:fadeUp .5s ease-out .15s both;
        }
        .auth-header h1{
            font-family:'Sora',sans-serif;
            font-size:1.3rem;font-weight:800;
            margin-bottom:.25rem;
        }
        .auth-header p{
            color:var(--auth-muted);
            font-size:.85rem;
        }

        .auth-form{animation:fadeUp .5s ease-out .25s both;}

        .form-group{margin-bottom:1rem;}
        .form-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:1rem;
        }
        @media(max-width:400px){
            .form-row{grid-template-columns:1fr;}
        }

        .form-group label{
            display:block;
            font-size:.75rem;font-weight:600;
            color:var(--auth-label);
            margin-bottom:.35rem;
            text-transform:uppercase;
            letter-spacing:.04em;
        }

        .input-wrap{position:relative;}
        .input-wrap .input-icon{
            position:absolute;left:.85rem;top:50%;
            transform:translateY(-50%);
            width:16px;height:16px;
            color:var(--auth-icon-color);
            transition:color .25s;
            pointer-events:none;
        }
        .input-wrap.focused .input-icon{color:#9d5cf5;}

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"]{
            width:100%;
            padding:.75rem .9rem .75rem 2.6rem;
            background:var(--auth-input-bg);
            border:1.5px solid var(--auth-input-border);
            border-radius:12px;
            color:var(--auth-input-color);
            font-size:.88rem;
            font-family:'Inter',sans-serif;
            transition:all .25s ease;
            outline:none;
        }
        .form-group input::placeholder{color:var(--auth-input-placeholder);}
        .form-group input:hover{border-color:rgba(124,58,237,.25);}
        .form-group input:focus{
            border-color:#7c3aed;
            background:rgba(124,58,237,.04);
            box-shadow:0 0 0 3px rgba(124,58,237,.12),inset 0 0 0 1px rgba(124,58,237,.15);
        }
        .form-group input.error{
            border-color:#ef4444;
            box-shadow:0 0 0 3px rgba(239,68,68,.1);
        }

        .toggle-pass{
            position:absolute;right:.75rem;top:50%;
            transform:translateY(-50%);
            background:none;border:none;
            color:var(--auth-icon-color);
            cursor:pointer;padding:.35rem;
            font-size:.85rem;
            transition:color .2s;
        }
        .toggle-pass:hover{color:var(--auth-label);}

        .form-group .error-text{
            font-size:.75rem;color:#f87171;
            margin-top:.3rem;
            display:flex;align-items:center;gap:.3rem;
        }

        .form-check{
            display:flex;align-items:center;justify-content:space-between;
            margin:1rem 0;
        }
        .form-check label{
            display:flex;align-items:center;gap:.5rem;
            font-size:.82rem;color:var(--auth-muted);
            cursor:pointer;
            text-transform:none;letter-spacing:0;
        }
        .form-check input[type="checkbox"]{
            width:16px;height:16px;
            accent-color:#7c3aed;
            cursor:pointer;
        }
        .form-check a{
            color:#9d5cf5;
            font-size:.82rem;text-decoration:none;
            font-weight:600;
            transition:opacity .2s;
        }
        .form-check a:hover{opacity:.8;text-decoration:underline;}

        .btn-primary{
            width:100%;
            padding:.8rem;
            background:linear-gradient(135deg,#9d5cf5,#7c3aed);
            color:#fff;
            border:none;
            border-radius:12px;
            font-size:.9rem;
            font-weight:700;
            font-family:'Inter',sans-serif;
            cursor:pointer;
            transition:all .25s ease;
            position:relative;
            display:flex;align-items:center;justify-content:center;
            gap:.5rem;
            box-shadow:0 4px 20px -6px rgba(124,58,237,.4);
        }
        .btn-primary:hover{
            transform:translateY(-2px);
            box-shadow:0 8px 30px -6px rgba(124,58,237,.55);
        }
        .btn-primary:active{transform:translateY(0);}
        .btn-primary:disabled{
            opacity:.6;cursor:not-allowed;
            transform:none;box-shadow:none;
        }
        .btn-primary .spinner{
            width:18px;height:18px;
            border:2px solid rgba(255,255,255,.3);
            border-top-color:#fff;
            border-radius:50%;
            animation:spin .6s linear infinite;
            display:none;
        }
        .btn-primary.loading .spinner{display:block;}
        .btn-primary.loading .btn-text{opacity:.7;}
        @keyframes spin{to{transform:rotate(360deg);}}

        .auth-footer-text{
            text-align:center;
            margin-top:.9rem;
            font-size:.85rem;color:var(--auth-muted);
            animation:fadeUp .4s ease-out .4s both;
        }
        .auth-footer-text a{
            color:#9d5cf5;
            text-decoration:none;font-weight:700;
            transition:opacity .2s;
        }
        .auth-footer-text a:hover{opacity:.8;text-decoration:underline;}

        .alert{
            padding:.7rem .9rem;border-radius:10px;
            font-size:.82rem;margin-bottom:1rem;
            display:flex;align-items:center;gap:.5rem;
            animation:fadeUp .3s ease-out;
        }
        .alert-success{
            background:rgba(52,211,153,.08);
            border:1px solid rgba(52,211,153,.15);
            color:#34d399;
        }
        .alert-error{
            background:rgba(239,68,68,.08);
            border:1px solid rgba(239,68,68,.15);
            color:#f87171;
        }

        .auth-divider{
            display:flex;align-items:center;gap:.75rem;
            margin:1rem 0;
            animation:fadeUp .4s ease-out .3s both;
        }
        .auth-divider::before,
        .auth-divider::after{
            content:'';flex:1;height:1px;
            background:var(--auth-card-border);
        }
        .auth-divider span{
            font-size:.78rem;color:var(--auth-muted);
            text-transform:uppercase;letter-spacing:.04em;
            white-space:nowrap;
        }

        .btn-google{
            display:flex;align-items:center;justify-content:center;gap:.6rem;
            width:100%;padding:.72rem;
            background:var(--auth-input-bg);
            border:1.5px solid var(--auth-input-border);
            border-radius:12px;
            color:var(--auth-text);
            font-size:.88rem;font-weight:600;
            font-family:'Inter',sans-serif;
            text-decoration:none;
            cursor:pointer;
            transition:all .25s ease;
            animation:fadeUp .5s ease-out .35s both;
        }
        .btn-google:hover{
            border-color:rgba(124,58,237,.35);
            background:var(--auth-card-bg);
            transform:translateY(-1px);
        }

        @keyframes fadeUp{
            from{opacity:0;transform:translateY(10px);}
            to{opacity:1;transform:translateY(0);}
        }

        @media(max-width:500px){
            .auth-page{padding:.8rem;}
            .auth-container{padding:1.8rem 1.25rem;border-radius:16px;}
            .auth-header h1{font-size:1.15rem;}
        }
    </style>
</head>
<body>
    <button class="auth-theme-btn" id="authThemeToggle" onclick="toggleAuthTheme()" aria-label="Ganti tema">
        <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
        <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
    </button>

    <div class="auth-bg-glow">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="glow glow-3"></div>
    </div>
    <div class="auth-grid"></div>

    <div class="auth-page">
        <div class="auth-container">
            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="auth-logo-img">
                <span class="auth-logo-text">JOHEN<span>GAMING</span></span>
            </a>

            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif
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

    <script>
    (function() {
        var theme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', theme);
    })();
    function toggleAuthTheme() {
        var html = document.documentElement;
        var current = html.getAttribute('data-theme');
        var next = current === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
    }
    </script>

    @stack('scripts')
</body>
</html>
