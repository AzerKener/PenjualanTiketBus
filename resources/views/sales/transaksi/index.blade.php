@extends('layouts.sales')
@section('title', 'Daftar Transaksi')
@section('content')

<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Transaksi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Riwayat pemesanan tiket yang Anda buat</p>
        </div>
        <a
            href="{{ route('sales.pemesanan.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Pemesanan Baru
        </a>
    </div>

    {{-- ===== STATISTIK CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Transaksi</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($totalTransaksi) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pendapatan</p>
                <p class="text-xl font-bold text-green-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Transaksi Pending</p>
                <p class="text-2xl font-bold text-amber-700">{{ number_format($transaksiPending) }}</p>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('sales.transaksi.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tanggal Mulai</label>
                    <input
                        type="date"
                        name="tanggal_dari"
                        value="{{ request('tanggal_dari') }}"
                        class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tanggal Akhir</label>
                    <input
                        type="date"
                        name="tanggal_sampai"
                        value="{{ request('tanggal_sampai') }}"
                        class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Status Pembayaran</label>
                    <select
                        name="status_pembayaran"
                        class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status_pembayaran') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('status_pembayaran') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'status_pembayaran']))
                    <a href="{{ route('sales.transaksi.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== TABEL TRANSAKSI ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">
                {{ $transaksis->total() }} transaksi ditemukan
            </h2>
            @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'status_pembayaran']))
                <span class="text-xs text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full font-medium">Filter aktif</span>
            @endif
        </div>

        @if($transaksis->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="font-semibold text-slate-600">Belum ada transaksi</p>
                <p class="text-slate-400 text-sm mt-1">Transaksi yang Anda buat akan muncul di sini</p>
                <a href="{{ route('sales.pemesanan.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                    Buat Pemesanan
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">ID</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Pemesan</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Rute</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Bus</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Tgl Berangkat</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Penumpang</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Metode</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Total</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Status</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">PP</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Tgl Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transaksis as $t)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                {{-- ID --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-slate-700 text-xs bg-slate-100 px-2 py-0.5 rounded">
                                        #{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Pemesan --}}
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800 whitespace-nowrap">{{ $t->nama_pemesan }}</p>
                                    <p class="text-xs text-slate-500">{{ $t->no_hp_pemesan }}</p>
                                </td>

                                {{-- Rute --}}
                                <td class="px-4 py-3">
                                    @if($t->jadwal && $t->jadwal->rute)
                                        <span class="whitespace-nowrap text-slate-700 font-medium">
                                            {{ $t->jadwal->rute->asal }}
                                            <span class="text-slate-400 mx-1">→</span>
                                            {{ $t->jadwal->rute->tujuan }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Bus --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($t->jadwal && $t->jadwal->bus)
                                        <p class="text-slate-700 font-medium">{{ $t->jadwal->bus->tipe_bus }}</p>
                                        <p class="text-xs text-slate-500">{{ $t->jadwal->bus->nomor_polisi }}</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Tanggal Berangkat --}}
                                <td class="px-4 py-3 whitespace-nowrap text-slate-700">
                                    @if($t->jadwal)
                                        <p>{{ $t->jadwal->tanggal_berangkat->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::substr($t->jadwal->waktu_berangkat, 0, 5) }} WIB</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Penumpang --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                                        {{ $t->penumpangs->count() }}
                                    </span>
                                </td>

                                {{-- Metode --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full
                                        {{ $t->metode_pembayaran === 'Cash' ? 'bg-green-100 text-green-700' : ($t->metode_pembayaran === 'Transfer' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                        {{ $t->metode_pembayaran }}
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="font-bold text-slate-800">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($t->status_pembayaran === 'lunas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></path></svg>
                                            Lunas
                                        </span>
                                    @elseif($t->status_pembayaran === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></path></svg>
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Gagal
                                        </span>
                                    @endif
                                </td>

                                {{-- PP --}}
                                <td class="px-4 py-3 text-center">
                                    @if($t->is_round_trip)
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full" title="Pulang-Pergi">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Tgl Transaksi --}}
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-500">
                                    {{ $t->tanggal_transaksi->format('d M Y') }}<br>
                                    {{ $t->tanggal_transaksi->format('H:i') }} WIB
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== FOOTER: TOTAL + PAGINATION ===== --}}
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    @php
                        $totalTampil = $transaksis->sum('total_bayar');
                    @endphp
                    <span class="font-medium">Total pendapatan (halaman ini):</span>
                    <span class="font-bold text-green-700 ml-1">Rp {{ number_format($totalTampil, 0, ',', '.') }}</span>
                    <span class="text-slate-400 ml-2">·</span>
                    <span class="ml-2 font-medium">Semua waktu:</span>
                    <span class="font-bold text-blue-700 ml-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                </div>
                <div>
                    {{ $transaksis->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
