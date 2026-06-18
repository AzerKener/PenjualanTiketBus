@extends('layouts.user')
@section('title', 'Daftar Akun — BusTicket')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-slate-100 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-7 text-center">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white">Buat Akun Baru</h1>
                <p class="text-blue-200 text-sm mt-1">Daftar gratis, pesan tiket dengan mudah</p>
            </div>

            {{-- Form --}}
            <div class="px-8 py-7">
                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5 text-sm text-red-700">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $e)
                        <li class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $e }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="Masukkan nama lengkap"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="contoh@email.com"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-300 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp') }}" required
                            placeholder="08xxxxxxxxxx"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_hp') border-red-300 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required minlength="8"
                            placeholder="Minimal 8 karakter"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-300 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                            placeholder="Ulangi password"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl text-sm transition-colors mt-2">
                        Buat Akun Sekarang
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-5">
                    Sudah punya akun?
                    <a href="{{ route('user.login') }}" class="text-blue-600 font-semibold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
