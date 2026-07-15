<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-gray-800 { background-color: #032D52 !important; }
        .bg-purple-600 { background-color: #0987F5 !important; }
        .hover\:bg-purple-700:hover { background-color: #0770cc !important; }
        .file\:bg-purple-600::file-selector-button { background-color: #0987F5 !important; }
        .hover\:file\:bg-purple-700::file-selector-button:hover { background-color: #0770cc !important; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-[#021B31] text-gray-100 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-[#032D52] flex-shrink-0">
            <div class="p-4 border-b border-[#043a66]">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-purple-400">
                    <i class="fas fa-crown mr-2"></i>Admin Panel
                </a>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.products') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.products*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-box w-5"></i>
                    <span>Produk</span>
                </a>
                <a href="{{ route('admin.brands') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.brands*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-gamepad w-5"></i>
                    <span>Daftar Game</span>
                </a>
                <a href="{{ route('admin.payment-methods') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.payment-methods*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-credit-card w-5"></i>
                    <span>Pembayaran</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.orders*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Pesanan</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-[#024073]' }}">
                    <i class="fas fa-cog w-5"></i>
                    <span>Pengaturan</span>
                </a>
                <hr class="border-[#043a66] my-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-[#024073]">
                    <i class="fas fa-arrow-left w-5"></i>
                    <span>Kembali ke Toko</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-[#024073]">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-[#032D52] px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-btn" class="md:hidden text-gray-300 hover:text-white">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-400">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-sm font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-4 flex items-center space-x-2">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-600 text-white px-4 py-3 rounded-lg mb-4 flex items-center space-x-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            let sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
