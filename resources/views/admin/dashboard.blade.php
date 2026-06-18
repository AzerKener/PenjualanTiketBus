@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}! Berikut ringkasan operasional hari ini.</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        {{-- Total Bus --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_bus'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Bus</p>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">Hari Ini</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['jadwal_hari_ini'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Jadwal</p>
        </div>

        {{-- Transaksi Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Hari Ini</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['transaksi_hari_ini'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Transaksi</p>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Hari Ini</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($stats['pendapatan_hari_ini'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1">Pendapatan</p>
        </div>

    </div>

    {{-- Jadwal Terkini --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Jadwal Terkini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Jadwal keberangkatan hari ini</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}"
               class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Lihat Semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Rute</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Bus</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Pool</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Jam</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwalTerkini ?? [] as $jadwal)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium text-slate-800">{{ $jadwal->rute->asal ?? '-' }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                <span class="text-sm font-medium text-slate-800">{{ $jadwal->rute->tujuan ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-700">{{ $jadwal->bus->no_polisi ?? '-' }}</span>
                            <span class="ml-1.5 text-xs text-slate-400">{{ $jadwal->bus->tipe_bus ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ $jadwal->pool->nama_pool ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-slate-700">{{ \Carbon\Carbon::parse($jadwal->waktu_berangkat)->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $jadwal->status ?? 'menunggu';
                                $badgeClass = match($status) {
                                    'berangkat' => 'bg-blue-100 text-blue-700',
                                    'selesai'   => 'bg-emerald-100 text-emerald-700',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                                $statusLabel = match($status) {
                                    'berangkat' => 'Berangkat',
                                    'selesai'   => 'Selesai',
                                    default     => 'Menunggu',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-slate-400">Belum ada jadwal hari ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
