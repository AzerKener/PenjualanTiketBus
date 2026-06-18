@extends('layouts.user')
@section('title', 'Riwayat Pemesanan — BusTicket')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Riwayat Pemesanan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Semua tiket yang pernah Anda pesan</p>
        </div>
        <a href="{{ route('user.home') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Pesan Tiket
        </a>
    </div>

    @forelse($pemesanans as $p)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-4 overflow-hidden card-hover">
        {{-- Top Bar --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="font-mono text-slate-500 text-sm">#{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</span>
            <div class="flex items-center gap-2">
                @if($p->is_round_trip)
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Pulang-Pergi</span>
                @endif
                @if($p->status_pembayaran === 'lunas')
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Lunas</span>
                @else
                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">Pending</span>
                @endif
                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->tanggal_transaksi)->isoFormat('D MMM Y HH:mm') }}</span>
            </div>
        </div>

        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Route --}}
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <p class="text-lg font-bold text-slate-800">
                        {{ $p->jadwal->rute->asal }} → {{ $p->jadwal->rute->tujuan }}
                    </p>
                    @if($p->is_round_trip)
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    @endif
                </div>
                <div class="text-sm text-slate-500 flex flex-wrap gap-3">
                    <span>{{ \Carbon\Carbon::parse($p->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }} • {{ substr($p->jadwal->waktu_berangkat, 0, 5) }}</span>
                    <span>{{ $p->jadwal->bus->nomor_polisi }} ({{ $p->jadwal->bus->tipe_bus }})</span>
                    <span>{{ $p->penumpangs->count() }} penumpang</span>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($p->penumpangs->where('jadwal_id', $p->jadwal_id)->take(4) as $pnp)
                    <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ $pnp->nomor_kursi }}</span>
                    @endforeach
                    @if($p->penumpangs->where('jadwal_id', $p->jadwal_id)->count() > 4)
                    <span class="text-xs text-slate-400">+{{ $p->penumpangs->where('jadwal_id', $p->jadwal_id)->count() - 4 }} lainnya</span>
                    @endif
                </div>
            </div>

            {{-- Price + Action --}}
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-slate-400">Total</p>
                    <p class="text-lg font-extrabold text-slate-800">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400">{{ $p->metode_pembayaran }}</p>
                </div>
                <a href="{{ route('user.etiket', $p->id) }}"
                    class="flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    E-Ticket
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
        </svg>
        <p class="text-slate-500 font-semibold text-lg">Belum ada pemesanan</p>
        <p class="text-slate-400 text-sm mt-1">Yuk, pesan tiket pertama Anda!</p>
        <a href="{{ route('user.home') }}"
            class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
            Cari Tiket Sekarang
        </a>
    </div>
    @endforelse

    @if($pemesanans->hasPages())
    <div class="mt-4">{{ $pemesanans->links() }}</div>
    @endif
</div>
@endsection
