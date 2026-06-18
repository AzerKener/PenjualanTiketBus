@extends('layouts.supir')
@section('title', 'Profil Akun')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Informasi akun pengguna Anda</p>
        </div>
        <a href="{{ route('supir.dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-8 sm:p-10 text-center border-b border-slate-100 bg-slate-50/50">
            <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-lg shadow-blue-600/20">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h2>
            <p class="text-slate-500 font-medium">{{ $user->email }}</p>
            
            <div class="mt-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Terverifikasi
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Detail Informasi</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <p class="font-medium text-slate-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Alamat Email</label>
                        <p class="font-medium text-slate-800">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Peran</label>
                        <p class="font-medium text-slate-800">{{ $user->role }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bergabung Sejak</label>
                        <p class="font-medium text-slate-800">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 pt-6 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-700">Catatan:</span> Saat ini profil bersifat hanya-baca (read-only). Jika ada perubahan data, silakan hubungi administrator.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
