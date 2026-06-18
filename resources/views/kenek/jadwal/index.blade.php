@extends('layouts.kenek')
@section('title', 'Jadwal Saya')

@section('content')
{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name }}</h1>
    <p class="text-slate-500 mt-1">Berikut adalah daftar jadwal keberangkatan Anda.</p>
</div>

@if(!$pegawai)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
    <svg class="w-12 h-12 text-amber-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <p class="text-amber-800 font-medium">Data pegawai tidak ditemukan</p>
    <p class="text-amber-600 text-sm mt-1">Nama akun Anda tidak cocok dengan data pegawai. Hubungi Admin.</p>
</div>
@else

{{-- Filter Tabs --}}
<div class="flex gap-2 mb-5" id="filter-tabs">
    <button onclick="filterJadwal('all')" id="tab-all"
        class="tab-btn px-4 py-2 rounded-xl text-sm font-medium bg-slate-800 text-white transition-all">
        Semua
    </button>
    <button onclick="filterJadwal('mendatang')" id="tab-mendatang"
        class="tab-btn px-4 py-2 rounded-xl text-sm font-medium bg-white text-slate-600 border border-slate-200 hover:border-slate-300 transition-all">
        Mendatang
    </button>
    <button onclick="filterJadwal('selesai')" id="tab-selesai"
        class="tab-btn px-4 py-2 rounded-xl text-sm font-medium bg-white text-slate-600 border border-slate-200 hover:border-slate-300 transition-all">
        Selesai
    </button>
</div>

@if($jadwals->isEmpty())
{{-- Empty State --}}
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <p class="text-slate-600 font-medium">Tidak ada jadwal terdaftar</p>
    <p class="text-slate-400 text-sm mt-1">Anda belum memiliki jadwal keberangkatan.</p>
</div>
@else

{{-- Jadwal Cards --}}
<div class="space-y-4" id="jadwal-list">
    @foreach($jadwals as $jadwal)
    @php
        $isMendatang = in_array($jadwal->status, ['menunggu', 'berangkat']);
        $isSelesai = $jadwal->status === 'selesai';
        $filterClass = $isMendatang ? 'filter-mendatang' : 'filter-selesai';
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow jadwal-card {{ $filterClass }}">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            {{-- Status Badge --}}
            <div>
                @if($jadwal->status === 'menunggu')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        <span class="w-1.5 h-1.5 bg-slate-500 rounded-full"></span>Menunggu
                    </span>
                @elseif($jadwal->status === 'berangkat')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>Berangkat
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Selesai
                    </span>
                @endif
            </div>
            {{-- Date --}}
            <p class="text-sm font-medium text-slate-500">
                {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('dddd, D MMMM Y') }}
            </p>
        </div>

        {{-- Route --}}
        <div class="mt-3 flex items-center gap-3">
            <div class="text-center">
                <p class="text-xl font-bold text-slate-800">{{ $jadwal->rute->asal }}</p>
            </div>
            <div class="flex-1 flex items-center gap-1">
                <div class="h-px flex-1 bg-slate-200"></div>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>
            <div class="text-center">
                <p class="text-xl font-bold text-slate-800">{{ $jadwal->rute->tujuan }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-400 font-medium">Jam Berangkat</p>
                <p class="text-sm font-bold text-slate-700 mt-0.5">{{ substr($jadwal->waktu_berangkat, 0, 5) }} WIB</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-400 font-medium">Bus</p>
                <p class="text-sm font-bold text-slate-700 mt-0.5 font-mono">{{ $jadwal->bus->nomor_polisi }}</p>
                <p class="text-xs text-slate-400">{{ $jadwal->bus->tipe_bus }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-400 font-medium">Pool</p>
                <p class="text-sm font-bold text-slate-700 mt-0.5">{{ $jadwal->pool->nama_pool }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-400 font-medium">Penumpang</p>
                <p class="text-sm font-bold text-slate-700 mt-0.5">{{ $jadwal->penumpangs->count() }} / {{ $jadwal->bus->jumlah_kursi }}</p>
            </div>
        </div>

        {{-- Crew --}}
        <div class="mt-3 flex flex-wrap gap-2">
            <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-blue-50 px-2.5 py-1.5 rounded-lg">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="font-medium">Supir 1:</span> {{ $jadwal->supir1->nama }}
            </div>
            @if($jadwal->supir2)
            <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-purple-50 px-2.5 py-1.5 rounded-lg">
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="font-medium">Supir 2:</span> {{ $jadwal->supir2->nama }}
            </div>
            @endif
            @if($jadwal->kenek)
            <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-green-50 px-2.5 py-1.5 rounded-lg">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="font-medium">Kenek:</span> {{ $jadwal->kenek->nama }}
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif
@endif

<script>
function filterJadwal(filter) {
    // Update tab styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.className = 'tab-btn px-4 py-2 rounded-xl text-sm font-medium bg-white text-slate-600 border border-slate-200 hover:border-slate-300 transition-all';
    });
    document.getElementById('tab-' + filter).className = 'tab-btn px-4 py-2 rounded-xl text-sm font-medium bg-slate-800 text-white transition-all';

    // Show/hide cards
    document.querySelectorAll('.jadwal-card').forEach(card => {
        if (filter === 'all') {
            card.style.display = '';
        } else if (filter === 'mendatang') {
            card.style.display = card.classList.contains('filter-mendatang') ? '' : 'none';
        } else if (filter === 'selesai') {
            card.style.display = card.classList.contains('filter-selesai') ? '' : 'none';
        }
    });
}
</script>
@endsection
