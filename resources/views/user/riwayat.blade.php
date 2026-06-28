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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Pesan Tiket
            </a>
        </div>

        @forelse($pemesanans as $p)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-4 overflow-hidden card-hover">
                {{-- Top Bar --}}
                <div class="flex justify-between items-start px-5 py-4 border-b bg-slate-50">

                    <div>

                        <p class="text-xs text-slate-400">
                            Kode Booking
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            #{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}
                        </p>

                    </div>

                    <div class="text-right">

                        @if ($p->status_pembayaran == 'lunas')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                                🟢 LUNAS
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">
                                🟡 MENUNGGU PEMBAYARAN
                            </span>
                        @endif

                        <p class="text-xs text-slate-400 mt-2">
                            Dipesan
                            {{ \Carbon\Carbon::parse($p->tanggal_transaksi)->isoFormat('D MMM Y HH:mm') }}
                        </p>

                    </div>

                </div>

                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{-- Route --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-lg font-bold text-slate-800 flex items-center gap-2">

                                🚌

                                {{ $p->jadwal->rute->asal }}

                                →

                                {{ $p->jadwal->rute->tujuan }}

                            </p>
                            @if ($p->is_round_trip)
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            @endif
                        </div>
                        <div class="text-sm text-slate-500 flex flex-wrap gap-3">
                            <span>{{ \Carbon\Carbon::parse($p->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }} •
                                {{ substr($p->jadwal->waktu_berangkat, 0, 5) }}</span>
                            <span>{{ $p->jadwal->bus->nomor_polisi }} ({{ $p->jadwal->bus->tipe_bus }})</span>
                            <span>{{ $p->penumpangs->count() }} penumpang</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach ($p->penumpangs->where('jadwal_id', $p->jadwal_id)->take(4) as $pnp)
                                <span class="bg-blue-50 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold">

                                    🎫 {{ $pnp->nomor_kursi }}

                                </span>
                            @endforeach
                            @if ($p->penumpangs->where('jadwal_id', $p->jadwal_id)->count() > 4)
                                <span
                                    class="text-xs text-slate-400">+{{ $p->penumpangs->where('jadwal_id', $p->jadwal_id)->count() - 4 }}
                                    lainnya</span>
                            @endif
                        </div>
                    </div>

                    {{-- Price + Action --}}
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-xs text-slate-400">Total</p>
                            <p class="text-lg font-extrabold text-slate-800">Rp
                                {{ number_format($p->total_bayar, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">{{ $p->metode_pembayaran }}</p>
                        </div>
                        <div class="flex flex-col gap-2">

                            @if ($p->status_pembayaran == 'lunas')
                                <a href="{{ route('user.etiket', $p->id) }}"
                                    class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition">

                                    🎫 E-Ticket

                                </a>
                            @else
                                <button disabled
                                    class="bg-slate-200 text-slate-500 px-4 py-2.5 rounded-xl text-xs font-semibold cursor-not-allowed">

                                    🔒 E-Ticket

                                </button>
                            @endif

                            <a href="{{ route('user.pesan.sukses', $p->id) }}"
                                class="border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-semibold text-center transition">

                                📄 Detail

                            </a>

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <p class="text-slate-500 font-semibold text-lg">Belum ada pemesanan</p>
                <p class="text-slate-400 text-sm mt-1">Yuk, pesan tiket pertama Anda!</p>
                <a href="{{ route('user.home') }}"
                    class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                    Cari Tiket Sekarang
                </a>
            </div>
        @endforelse

        @if ($pemesanans->hasPages())
            <div class="mt-4">{{ $pemesanans->links() }}</div>
        @endif
    </div>
@endsection
