@extends('layouts.sales')
@section('title', 'Dashboard Loket')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p class="text-sm text-slate-500 mt-1">Ringkasan penjualan tiket Anda hari ini — {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xs text-slate-500 font-medium">Penjualan Hari Ini</p>
        <p class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        </div>
        <p class="text-xs text-slate-500 font-medium">Tiket Terjual Hari Ini</p>
        <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $tiketHariIni }} <span class="text-sm font-normal text-slate-400">kursi</span></p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-xs text-slate-500 font-medium">Total Penjualan</p>
        <p class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-xs text-slate-500 font-medium">Total Tiket Terjual</p>
        <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $totalTiket }} <span class="text-sm font-normal text-slate-400">kursi</span></p>
    </div>
</div>

<!-- Quick Action -->
<div class="mb-6">
    <a href="{{ route('sales.pemesanan.index') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Pesan Tiket Baru
    </a>
</div>

<!-- Transaksi Terbaru -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-800">Transaksi Terakhir</h2>
        <a href="{{ route('sales.transaksi.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-xs uppercase tracking-wider text-slate-500">
                    <th class="p-4 font-semibold">Waktu</th>
                    <th class="p-4 font-semibold">Pemesan</th>
                    <th class="p-4 font-semibold">Rute</th>
                    <th class="p-4 font-semibold">Total</th>
                    <th class="p-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transaksiTerbaru as $trx)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-sm text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M, H:i') }}</td>
                    <td class="p-4">
                        <p class="text-sm font-medium text-slate-800">{{ $trx->nama_pemesan }}</p>
                        <p class="text-xs text-slate-400">{{ $trx->no_hp_pemesan }}</p>
                    </td>
                    <td class="p-4 text-sm text-slate-600">
                        @if($trx->jadwal && $trx->jadwal->rute)
                            {{ $trx->jadwal->rute->asal }} → {{ $trx->jadwal->rute->tujuan }}
                        @else <span class="italic text-slate-400">–</span> @endif
                    </td>
                    <td class="p-4 text-sm font-bold text-slate-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Lunas
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-slate-400 text-sm">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
