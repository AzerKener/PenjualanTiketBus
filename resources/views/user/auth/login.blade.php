@extends('layouts.user')
@section('title', 'Masuk — BusTicket')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-slate-100 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-7 text-center">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white">Masuk ke BusTicket</h1>
                <p class="text-blue-200 text-sm mt-1">Lihat riwayat & kelola pemesanan Anda</p>
            </div>

            <div class="px-8 py-7">
                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5 text-sm text-red-700">
                    @foreach($errors->all() as $e)
                    <p class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ $e }}
                    </p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('user.login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="contoh@email.com"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-300 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            placeholder="••••••••"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="rounded text-blue-600">
                        <label for="remember" class="ml-2 text-sm text-slate-600">Ingat saya</label>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl text-sm transition-colors">
                        Masuk
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-5">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar gratis</a>
                </p>
                <p class="text-center text-xs text-slate-400 mt-3">
                    Staff/Admin? <a href="{{ route('login') }}" class="text-slate-500 hover:underline">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
