@extends('layouts.sales')
@section('title', 'Profil Akun')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Informasi akun Petugas Loket</p>
        </div>
        <a href="{{ route('sales.dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-8 text-center border-b border-slate-100 bg-gradient-to-b from-amber-50 to-white">
            <div class="w-20 h-20 bg-amber-500 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-lg shadow-amber-500/30">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-slate-500 text-sm">{{ $user->email }}</p>
            <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                🎫 Petugas Loket (Sales)
            </span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                    <p class="font-medium text-slate-800">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Peran</label>
                    <p class="font-medium text-slate-800">Sales / Petugas Loket</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bergabung Sejak</label>
                    <p class="font-medium text-slate-800">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-700">Catatan:</span> Untuk mengubah data akun, silakan hubungi Administrator.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
