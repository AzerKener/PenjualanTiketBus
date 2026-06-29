@extends('layouts.sales')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Riwayat Transaksi</h1>
        <p class="text-sm text-slate-500 mt-1">Semua transaksi yang Anda buat di loket.</p>
    </div>
    <a href="{{ route('sales.pemesanan.index') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors">
        + Pesan Baru
    </a>
</div>

<!-- Filter & Cari -->
<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Tiket / No. HP</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: 15 atau 0812..."
               class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 min-w-[200px]">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter Tanggal</label>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
               class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
    </div>
    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">Cari</button>
    @if(request('tanggal') || request('search'))
    <a href="{{ route('sales.transaksi.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors">Reset</a>
    @endif
</form>

<!-- Table -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-xs uppercase tracking-wider text-slate-500">
                    <th class="p-4 font-semibold">No.</th>
                    <th class="p-4 font-semibold">Waktu Transaksi</th>
                    <th class="p-4 font-semibold">Pemesan</th>
                    <th class="p-4 font-semibold">Rute</th>
                    <th class="p-4 font-semibold">Kursi</th>
                    <th class="p-4 font-semibold">Total</th>
                    <th class="p-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transaksis as $trx)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-sm text-slate-400">{{ $transaksis->firstItem() + $loop->index }}</td>
                    <td class="p-4 text-sm text-slate-600">
                        {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }}<br>
                        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('H:i') }} WIB</span>
                    </td>
                    <td class="p-4">
                        <p class="text-sm font-medium text-slate-800">{{ $trx->nama_pemesan }}</p>
                        <p class="text-xs text-slate-400">{{ $trx->no_hp_pemesan }}</p>
                    </td>
                    <td class="p-4 text-sm">
                        @if($trx->jadwal && $trx->jadwal->rute)
                            <span class="font-medium text-slate-800">{{ $trx->jadwal->rute->asal }} → {{ $trx->jadwal->rute->tujuan }}</span><br>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($trx->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}</span>
                            @if($trx->is_round_trip)
                                <span class="ml-1 text-xs bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded-full font-medium">PP</span>
                            @endif
                        @else
                            <span class="italic text-slate-400">–</span>
                        @endif
                    </td>
                    <td class="p-4 text-sm text-center font-bold text-slate-700">{{ $trx->penumpangs->count() }}</td>
                    <td class="p-4 text-sm font-bold text-slate-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4">
                        @if($trx->status_pembayaran === 'lunas')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Lunas
                            </span>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span> Pending
                                </span>
                                <form method="POST" action="{{ route('sales.transaksi.konfirmasi', $trx->id) }}" onsubmit="return confirm('Konfirmasi bahwa uang cash sudah diterima dari {{ $trx->nama_pemesan }} sejumlah Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                        Konfirmasi Bayar
                                    </button>
                                </form>
                            </div>
                        @endif
                        @if($trx->tipe_pemesanan === 'Online')
                            <span class="block mt-1 text-[10px] uppercase font-bold text-blue-500">Pemesanan Online</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-slate-400 text-sm">
                        Belum ada transaksi
                        @if(request('tanggal')) pada tanggal {{ \Carbon\Carbon::parse(request('tanggal'))->isoFormat('D MMMM Y') }} @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transaksis->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $transaksis->links() }}
    </div>
    @endif
</div>
@endsection
