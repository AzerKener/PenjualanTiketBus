@extends('layouts.user')
@section('title', 'BusTicket — Pesan Tiket Bus Online')
@section('meta-description', 'Pesan tiket bus online dengan mudah. Pilih rute, jadwal, dan kursi favoritmu sekarang.')

@section('content')

{{-- HERO SECTION --}}
<section class="gradient-hero text-white py-16 lg:py-24 relative overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">
                Perjalanan Nyaman<br class="hidden sm:block">
                <span class="text-blue-300">Dimulai dari Sini</span>
            </h1>
            <p class="text-blue-100 text-lg max-w-xl mx-auto">
                Pesan tiket bus ke seluruh tujuan dengan harga terbaik, kursi terjamin, dan proses yang mudah.
            </p>
        </div>

        {{-- Search Card --}}
        <div class="max-w-4xl mx-auto glass rounded-3xl p-6 sm:p-8 shadow-2xl">
            <form method="GET" action="{{ route('user.cari') }}" id="searchForm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Asal --}}
                    <div>
                        <label class="block text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">Kota Asal</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <input list="asal-list" name="asal" value="{{ request('asal') }}"
                                placeholder="cth. Jakarta"
                                class="w-full bg-white/10 border border-white/20 rounded-xl pl-9 pr-3 py-3 text-white placeholder-blue-300 text-sm focus:outline-none focus:border-white/50 focus:bg-white/15 transition-all">
                            <datalist id="asal-list">
                                @foreach($asalList as $a)<option value="{{ $a }}" class="text-slate-800">@endforeach
                            </datalist>
                        </div>
                    </div>

                    {{-- Tujuan --}}
                    <div>
                        <label class="block text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">Kota Tujuan</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <input list="tujuan-list" name="tujuan" value="{{ request('tujuan') }}"
                                placeholder="cth. Surabaya"
                                class="w-full bg-white/10 border border-white/20 rounded-xl pl-9 pr-3 py-3 text-white placeholder-blue-300 text-sm focus:outline-none focus:border-white/50 focus:bg-white/15 transition-all">
                            <datalist id="tujuan-list">
                                @foreach($tujuanList as $t)<option value="{{ $t }}" class="text-slate-800">@endforeach
                            </datalist>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">Tanggal Berangkat</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" name="tanggal_berangkat" value="{{ request('tanggal_berangkat', now()->addDay()->format('Y-m-d')) }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full bg-white/10 border border-white/20 rounded-xl pl-9 pr-3 py-3 text-white text-sm focus:outline-none focus:border-white/50 focus:bg-white/15 transition-all">
                        </div>
                    </div>

                    {{-- Tipe Bus + Tombol --}}
                    <div class="flex flex-col gap-2">
                        <label class="block text-xs font-semibold text-blue-200 uppercase tracking-wider mb-0">Tipe Bus</label>
                        <select name="tipe_bus"
                            class="bg-white/10 border border-white/20 rounded-xl px-3 py-3 text-white text-sm focus:outline-none focus:border-white/50 transition-all">
                            <option value="" class="text-slate-800">Semua Tipe</option>
                            <option value="Ekonomi" class="text-slate-800" {{ request('tipe_bus') === 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                            <option value="VIP" class="text-slate-800" {{ request('tipe_bus') === 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="Executive" class="text-slate-800" {{ request('tipe_bus') === 'Executive' ? 'selected' : '' }}>Executive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit"
                        class="w-full bg-white text-blue-700 font-bold py-3.5 px-8 rounded-xl text-sm hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Tiket Bus
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Feature Badges --}}
<section class="bg-white border-b border-slate-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-slate-600">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="font-medium">Pemesanan Aman</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">Konfirmasi Instan</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span class="font-medium">Cash, Transfer, E-Wallet</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span class="font-medium">Tiket Pulang-Pergi</span>
            </div>
        </div>
    </div>
</section>

{{-- Jadwal Mendatang --}}
@if(isset($jadwalMendatang) && $jadwalMendatang->isNotEmpty())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800">Jadwal Tersedia Segera</h2>
            <a href="{{ route('user.cari', ['asal' => '', 'tujuan' => '', 'tanggal_berangkat' => now()->addDay()->format('Y-m-d')]) }}" class="text-sm text-blue-600 hover:underline font-medium">Lihat semua →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($jadwalMendatang as $j)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 card-hover shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-lg font-bold text-slate-800">{{ $j->rute->asal }} → {{ $j->rute->tujuan }}</p>
                        <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($j->tanggal_berangkat)->isoFormat('D MMM Y') }} • {{ substr($j->waktu_berangkat, 0, 5) }}</p>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold">{{ $j->bus->tipe_bus }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div>
                        <p class="font-bold text-green-700 text-base">Rp {{ number_format($j->harga_tiket, 0, ',', '.') }}</p>
                        <p class="text-slate-400 text-xs">{{ $j->pool->nama_pool }}</p>
                    </div>
                    @auth
                        @if(auth()->user()->role === 'User')
                        <a href="{{ route('user.pesan.show', $j->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors">
                            Pesan
                        </a>
                        @else
                        <a href="{{ route('user.login') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors">
                            Pesan
                        </a>
                        @endif
                    @else
                    <a href="{{ route('user.login') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors">
                        Pesan Sekarang
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
