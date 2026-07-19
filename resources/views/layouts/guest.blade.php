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
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{
            font-family:'Inter',sans-serif;
            background:#100821;
            color:#f5f3fb;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            overflow:hidden;
        }

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
        @keyframes glowFloat{
            0%,100%{transform:translate(0,0) scale(1);}
            25%{transform:translate(40px,-50px) scale(1.08);}
            50%{transform:translate(-30px,30px) scale(.92);}
            75%{transform:translate(50px,40px) scale(1.05);}
        }

        .auth-grid{
            position:fixed;inset:0;pointer-events:none;z-index:0;
            background-image:
                linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);
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
            background:rgba(30,17,54,.7);
            backdrop-filter:blur(30px);
            border:1px solid rgba(255,255,255,.06);
            border-radius:20px;
            padding:2rem 1.8rem;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.02),
                0 30px 80px -20px rgba(0,0,0,.6),
                0 0 80px -20px rgba(124,58,237,.08);
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
            font-weight:800;font-size:1.15rem;color:#f5f3fb;
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
            color:#7c6ea3;
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
            color:#b3a6d6;
            margin-bottom:.35rem;
            text-transform:uppercase;
            letter-spacing:.04em;
        }

        .input-wrap{position:relative;}
        .input-wrap .input-icon{
            position:absolute;left:.85rem;top:50%;
            transform:translateY(-50%);
            width:16px;height:16px;
            color:#5a4a7a;
            transition:color .25s;
            pointer-events:none;
        }
        .input-wrap.focused .input-icon{color:#9d5cf5;}

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"]{
            width:100%;
            padding:.75rem .9rem .75rem 2.6rem;
            background:rgba(255,255,255,.04);
            border:1.5px solid rgba(255,255,255,.07);
            border-radius:12px;
            color:#f5f3fb;
            font-size:.88rem;
            font-family:'Inter',sans-serif;
            transition:all .25s ease;
            outline:none;
        }
        .form-group input::placeholder{color:#3d2d5c;}
        .form-group input:hover{border-color:rgba(255,255,255,.14);}
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
            color:#5a4a7a;
            cursor:pointer;padding:.35rem;
            font-size:.85rem;
            transition:color .2s;
        }
        .toggle-pass:hover{color:#b3a6d6;}

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
            font-size:.82rem;color:#7c6ea3;
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
            font-size:.85rem;color:#7c6ea3;
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

    @stack('scripts')
</body>
</html>
