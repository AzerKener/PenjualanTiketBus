@extends('layouts.sales')
@section('title', 'Pesan Tiket')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Pesan Tiket</h1>
    <p class="text-sm text-slate-500 mt-1">Cari jadwal bus dan pesan tiket untuk pelanggan.</p>
</div>

<!-- Form Pencarian -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
        <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        Cari Jadwal
    </h2>
    <form method="POST" action="{{ route('sales.pemesanan.cari') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kota Asal</label>
            <select name="asal" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">-- Pilih Asal --</option>
                @foreach($asalList as $a)
                    <option value="{{ $a }}" {{ (old('asal', request('asal') ?? ($search['asal'] ?? '')) == $a) ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kota Tujuan</label>
            <select name="tujuan" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">-- Pilih Tujuan --</option>
                @foreach($tujuanList as $t)
                    <option value="{{ $t }}" {{ (old('tujuan', request('tujuan') ?? ($search['tujuan'] ?? '')) == $t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Berangkat</label>
            <input type="date" name="tanggal_berangkat" required
                   value="{{ old('tanggal_berangkat', $search['tanggal_berangkat'] ?? date('Y-m-d')) }}"
                   min="{{ date('Y-m-d') }}"
                   class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipe Bus</label>
            <select name="tipe_bus" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Semua Tipe</option>
                @foreach($tipesBus as $t)
                    <option value="{{ $t }}" {{ (old('tipe_bus', $search['tipe_bus'] ?? '') == $t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2 lg:col-span-4">
            <button type="submit" class="w-full sm:w-auto px-8 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors">
                Cari Jadwal
            </button>
        </div>
    </form>
</div>

<!-- Hasil Pencarian -->
@isset($jadwals)
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-600">
        Ditemukan <span class="font-bold text-slate-800">{{ count($jadwals) }}</span> jadwal
        @isset($search)
            untuk rute <span class="font-semibold text-amber-700">{{ $search['asal'] }} → {{ $search['tujuan'] }}</span>
            tanggal <span class="font-semibold">{{ \Carbon\Carbon::parse($search['tanggal_berangkat'])->isoFormat('D MMMM Y') }}</span>
        @endisset
    </p>
</div>

@if($jadwals->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="font-semibold text-slate-600">Tidak ada jadwal tersedia</p>
        <p class="text-sm text-slate-400 mt-1">Coba ubah rute atau tanggal pencarian.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($jadwals as $jadwal)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:shadow-md hover:border-amber-200 transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Route & Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-lg font-extrabold text-slate-800">{{ $jadwal->rute->asal }}</span>
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <span class="text-lg font-extrabold text-slate-800">{{ $jadwal->rute->tujuan }}</span>
                        @php
                            $badgeColor = match($jadwal->bus->tipe_bus) {
                                'Eksekutif' => 'bg-purple-100 text-purple-700',
                                'Bisnis'    => 'bg-blue-100 text-blue-700',
                                default     => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">{{ $jadwal->bus->tipe_bus }}</span>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-slate-500">
                        <span class="flex items-center gap-1">🕐 {{ substr($jadwal->waktu_berangkat, 0, 5) }} WIB</span>
                        <span class="flex items-center gap-1">🚌 {{ $jadwal->bus->nomor_polisi }}</span>
                        <span class="flex items-center gap-1">📍 {{ $jadwal->pool->nama_pool }}</span>
                        <span class="flex items-center gap-1 {{ $jadwal->kursi_tersedia > 0 ? 'text-green-600' : 'text-red-500' }}">
                            💺 {{ $jadwal->kursi_tersedia }} kursi tersedia
                        </span>
                    </div>
                </div>
                <!-- Price & Action -->
                <div class="text-right flex-shrink-0">
                    <p class="text-2xl font-extrabold text-amber-600">Rp {{ number_format($jadwal->harga_tiket, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mb-3">per kursi</p>
                    @if($jadwal->kursi_tersedia > 0)
                        <a href="{{ route('sales.pemesanan.pilih', $jadwal->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors">
                            Pilih Kursi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @else
                        <span class="inline-flex items-center px-5 py-2.5 bg-slate-100 text-slate-400 font-semibold rounded-xl text-sm cursor-not-allowed">
                            Penuh
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endisset

@endsection
