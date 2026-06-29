@extends('layouts.user')
@section('title', 'Hasil Pencarian — BusTicket')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Search Form (compact) --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('user.cari') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Asal</label>
                <input list="asal-list2" name="asal" value="{{ $request->asal }}"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <datalist id="asal-list2">
                    @foreach($asalList as $a)<option value="{{ $a }}">@endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tujuan</label>
                <input list="tujuan-list2" name="tujuan" value="{{ $request->tujuan }}"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <datalist id="tujuan-list2">
                    @foreach($tujuanList as $t)<option value="{{ $t }}">@endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal_berangkat" value="{{ $request->tanggal_berangkat }}" min="{{ now()->format('Y-m-d') }}"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe Bus</label>
                <select name="tipe_bus" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="Ekonomi" {{ $request->tipe_bus === 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="VIP" {{ $request->tipe_bus === 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="Executive" {{ $request->tipe_bus === 'Executive' ? 'selected' : '' }}>Executive</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                Cari Ulang
            </button>
        </form>
    </div>

    {{-- Results Header --}}
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-bold text-slate-800">
            @if($jadwals->isNotEmpty())
                {{ $jadwals->count() }} jadwal tersedia:
                <span class="text-blue-600">{{ $request->asal }} → {{ $request->tujuan }}</span>
            @else
                Tidak ada jadwal ditemukan
            @endif
        </h1>
        @if($request->tanggal_berangkat)
        <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($request->tanggal_berangkat)->isoFormat('dddd, D MMMM Y') }}</p>
        @endif
    </div>

    @forelse($jadwals as $j)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-4 card-hover">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            {{-- Route + Time --}}
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-2xl font-extrabold text-slate-800">{{ substr($j->waktu_berangkat, 0, 5) }}</span>
                    <div class="flex-1 flex items-center gap-1">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-slate-800">{{ $j->rute->tujuan }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        {{ $j->bus->nomor_polisi }} • {{ $j->bus->tipe_bus }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $j->pool->nama_pool }} → {{ $j->poolTujuan ? $j->poolTujuan->nama_pool : '-' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Sisa {{ $j->kursi_tersedia }} kursi
                    </span>
                </div>
            </div>

            {{-- Price + CTA --}}
            <div class="flex sm:flex-col items-center sm:items-end gap-3">
                <div class="text-right">
                    <p class="text-2xl font-extrabold text-green-600">Rp {{ number_format($j->harga_tiket, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400">per kursi</p>
                </div>
                @if($j->kursi_tersedia > 0)
                    @auth
                        @if(auth()->user()->role === 'User')
                        <a href="{{ route('user.pesan.show', $j->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap">
                            Pilih Kursi
                        </a>
                        @else
                        <a href="{{ route('user.login') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            Masuk untuk Pesan
                        </a>
                        @endif
                    @else
                    <a href="{{ route('user.login') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors whitespace-nowrap">
                        Masuk untuk Pesan
                    </a>
                    @endauth
                @else
                    <span class="bg-slate-100 text-slate-500 px-5 py-2.5 rounded-xl text-sm font-medium">Penuh</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-slate-500 font-semibold text-lg">Tidak ada jadwal tersedia</p>
        <p class="text-slate-400 text-sm mt-2">Coba ubah tanggal atau rute pencarian Anda.</p>
        <a href="{{ route('user.home') }}" class="mt-4 inline-block text-blue-600 hover:underline text-sm font-medium">← Kembali ke beranda</a>
    </div>
    @endforelse
</div>
@endsection
