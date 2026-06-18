@extends('layouts.admin')
@section('page-title', 'Daftar Transaksi')
@section('page-subtitle', 'Semua transaksi pemesanan tiket')

@section('content')
{{-- Filter --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('admin.transaksi.index') }}"
        class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai</label>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Pool</label>
            <select name="pool_id" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Pool</option>
                @foreach($pools as $pool)
                    <option value="{{ $pool->id }}" {{ request('pool_id') == $pool->id ? 'selected' : '' }}>
                        {{ $pool->nama_pool }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status_pembayaran" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status_pembayaran') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="pending" {{ request('status_pembayaran') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.transaksi.index') }}"
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
        <p class="text-2xl font-bold text-slate-800">{{ $totalTransaksi }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Transaksi</p>
    </div>
    <div class="bg-green-50 rounded-xl border border-green-100 p-4 text-center shadow-sm">
        <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        <p class="text-xs text-green-500 mt-1">Total Pendapatan</p>
    </div>
    <div class="bg-amber-50 rounded-xl border border-amber-100 p-4 text-center shadow-sm">
        <p class="text-2xl font-bold text-amber-700">{{ $transaksiPending }}</p>
        <p class="text-xs text-amber-500 mt-1">Pending</p>
    </div>
    <div class="bg-blue-50 rounded-xl border border-blue-100 p-4 text-center shadow-sm">
        <p class="text-2xl font-bold text-blue-700">{{ $transaksiLunas }}</p>
        <p class="text-xs text-blue-500 mt-1">Lunas</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-semibold text-slate-800">Riwayat Transaksi</h2>
        <span class="text-sm text-slate-500">{{ $pemesanans->total() }} transaksi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">ID</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nama Pemesan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Rute</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Bus</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Pool</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tgl. Berangkat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tipe</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Total</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">PP</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Penumpang</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tgl. Transaksi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pemesanans as $p)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400 text-xs">#{{ $p->id }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">{{ $p->nama_pemesan }}</td>
                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                        {{ $p->jadwal->rute->asal }} → {{ $p->jadwal->rute->tujuan }}
                    </td>
                    <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap text-xs">{{ $p->jadwal->bus->nomor_polisi }}</td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $p->jadwal->pool->nama_pool }}</td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap text-xs">
                        {{ \Carbon\Carbon::parse($p->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}
                        {{ substr($p->jadwal->waktu_berangkat,0,5) }}
                    </td>
                    <td class="px-4 py-3">
                        @if($p->tipe_pemesanan === 'Online')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Online</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Sales Pool</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">{{ $p->metode_pembayaran }}</span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-slate-800 whitespace-nowrap">
                        Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($p->status_pembayaran === 'lunas')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lunas</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->is_round_trip)
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">PP</span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-slate-700 font-medium">{{ $p->penumpangs->count() }}</td>
                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap text-xs">
                        {{ \Carbon\Carbon::parse($p->tanggal_transaksi)->isoFormat('D MMM Y HH:mm') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        @if($p->status_pembayaran === 'pending')
                        <form action="{{ route('admin.transaksi.konfirmasi', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi pembayaran telah diterima dan ubah status menjadi Lunas?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                Konfirmasi
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Belum ada transaksi ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pemesanans->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $pemesanans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
