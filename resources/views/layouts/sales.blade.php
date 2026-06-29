<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sales') — BusTicket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-slate-50" x-data="{ navOpen: false }">

    <!-- Navbar -->
    <nav class="bg-amber-600 sticky top-0 z-20 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">

                <!-- Brand -->
                <a href="{{ route('sales.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-sm">BusTicket <span class="text-amber-200 font-normal">Loket</span></span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('sales.dashboard') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('sales.dashboard') ? 'bg-white/20 text-white' : 'text-amber-100 hover:bg-white/10 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('sales.pemesanan.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('sales.pemesanan.*') ? 'bg-white/20 text-white' : 'text-amber-100 hover:bg-white/10 hover:text-white' }}">
                        Pesan Tiket
                    </a>
                    <a href="{{ route('sales.transaksi.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('sales.transaksi.*') ? 'bg-white/20 text-white' : 'text-amber-100 hover:bg-white/10 hover:text-white' }}">
                        Transaksi
                    </a>
                </div>

                <!-- User + Actions -->
                <div class="flex items-center gap-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-amber-200">Petugas Loket</p>
                    </div>
                    <a href="{{ route('notifikasi.index') }}" class="relative p-2 text-amber-200 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-amber-600"></span>
                        @endif
                    </a>
                    <a href="{{ route('sales.akun.index') }}"
                       class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-white/20 text-white hover:bg-white/30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/30 text-white hover:bg-red-500/50 transition-colors">
                            Keluar
                        </button>
                    </form>
                    <!-- Mobile hamburger -->
                    <button @click="navOpen = !navOpen" class="md:hidden p-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="navOpen" x-cloak class="md:hidden border-t border-amber-500 bg-amber-700">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('sales.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-100 hover:bg-white/10">Dashboard</a>
                <a href="{{ route('sales.pemesanan.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-100 hover:bg-white/10">Pesan Tiket</a>
                <a href="{{ route('sales.transaksi.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-100 hover:bg-white/10">Transaksi</a>
                <a href="{{ route('sales.akun.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-100 hover:bg-white/10">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-300 hover:bg-white/10">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
