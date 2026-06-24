@extends('layouts.sales')
@section('title', 'Pemesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header sukses -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-8 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold">Pemesanan Berhasil!</h1>
            <p class="text-amber-100 mt-1 text-sm">Tiket telah dipesan dan pembayaran diterima</p>
            <div class="mt-3 inline-block bg-white/20 rounded-xl px-4 py-1.5">
                <span class="font-mono font-bold text-lg">#{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="p-6 space-y-5">
            <!-- Info Pemesan -->
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Data Pemesan</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs">Nama</p>
                        <p class="font-semibold text-slate-800">{{ $pemesanan->nama_pemesan }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">No. HP</p>
                        <p class="font-semibold text-slate-800">{{ $pemesanan->no_hp_pemesan }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Status Bayar</p>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">✓ Lunas</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Metode</p>
                        <p class="font-semibold text-slate-800">Cash</p>
                    </div>
                </div>
            </div>

            <!-- Tiket Pergi -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="bg-amber-50 px-4 py-3 flex justify-between items-center">
                    <span class="font-bold text-amber-800 text-sm">
                        {{ $pemesanan->jadwal->rute->asal }} → {{ $pemesanan->jadwal->rute->tujuan }}
                    </span>
                    <span class="text-xs text-amber-600">
                        {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}
                        {{ substr($pemesanan->jadwal->waktu_berangkat, 0, 5) }}
                    </span>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($pemesanan->penumpangsPergi as $p)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded">{{ $p->nomor_kursi }}</span>
                            <span class="text-slate-700">{{ $p->nama_penumpang }}</span>
                        </div>
                        <span class="text-slate-600 font-medium">Rp {{ number_format($pemesanan->jadwal->harga_tiket, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tiket Pulang (jika ada) -->
            @if($pemesanan->is_round_trip && $pemesanan->jadwalPulang)
            <div class="border border-purple-200 rounded-2xl overflow-hidden">
                <div class="bg-purple-50 px-4 py-3 flex justify-between items-center">
                    <span class="font-bold text-purple-800 text-sm">
                        {{ $pemesanan->jadwalPulang->rute->asal }} → {{ $pemesanan->jadwalPulang->rute->tujuan }}
                        <span class="text-purple-400 font-normal text-xs">(pulang)</span>
                    </span>
                    <span class="text-xs text-purple-600">
                        {{ \Carbon\Carbon::parse($pemesanan->jadwalPulang->tanggal_berangkat)->isoFormat('D MMM Y') }}
                    </span>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($pemesanan->penumpangsPulang as $p)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">{{ $p->nomor_kursi }}</span>
                            <span class="text-slate-700">{{ $p->nama_penumpang }}</span>
                        </div>
                        <span class="text-slate-600 font-medium">Rp {{ number_format($pemesanan->jadwalPulang->harga_tiket, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Total -->
            <div class="bg-slate-800 text-white rounded-2xl p-4 flex items-center justify-between">
                <span class="font-semibold">Total Pembayaran</span>
                <span class="text-2xl font-extrabold text-amber-400">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</span>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex gap-3">
                <a href="{{ route('sales.pemesanan.index') }}"
                   class="flex-1 text-center px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors">
                    Pesan Tiket Lagi
                </a>
                <a href="{{ route('sales.transaksi.index') }}"
                   class="flex-1 text-center px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-sm transition-colors">
                    Lihat Transaksi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
