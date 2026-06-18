@extends('layouts.admin')
@section('page-title', 'Akun Saya')
@section('page-subtitle', 'Informasi profil akun')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Header / Avatar --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-8 flex flex-col sm:flex-row items-center gap-5">
            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center text-3xl font-bold text-white flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="text-center sm:text-left">
                <h2 class="text-2xl font-bold text-white">{{ auth()->user()->name }}</h2>
                <p class="text-blue-200 mt-1">{{ auth()->user()->email }}</p>
                <span class="inline-flex items-center mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold text-white">
                    {{ auth()->user()->role }}
                </span>
            </div>
        </div>

        {{-- Info Rows - READ ONLY, NO SAVE BUTTON --}}
        <div class="divide-y divide-slate-100">
            <div class="flex items-center gap-4 px-8 py-5">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Nama Lengkap</p>
                    <p class="text-base font-semibold text-slate-800 mt-0.5">{{ auth()->user()->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-8 py-5">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Email</p>
                    <p class="text-base font-semibold text-slate-800 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 px-8 py-5">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Role / Jabatan</p>
                    <p class="text-base font-semibold text-slate-800 mt-0.5">{{ auth()->user()->role }}</p>
                </div>
            </div>

            @if(auth()->user()->pool)
            <div class="flex items-center gap-4 px-8 py-5">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Pool</p>
                    <p class="text-base font-semibold text-slate-800 mt-0.5">{{ auth()->user()->pool->nama_pool }}</p>
                    <p class="text-sm text-slate-500">{{ auth()->user()->pool->lokasi }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-4 px-8 py-5">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Bergabung Sejak</p>
                    <p class="text-base font-semibold text-slate-800 mt-0.5">
                        {{ auth()->user()->created_at->isoFormat('D MMMM Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer note - no save button intentionally --}}
        <div class="px-8 py-5 bg-slate-50 border-t border-slate-100">
            <p class="text-xs text-slate-400 text-center">
                Untuk mengubah data akun, hubungi Super Admin sistem.
            </p>
        </div>
    </div>
</div>
@endsection
