@extends('layouts.sales')
@section('title', 'Pemesanan Berhasil')
@section('content')

<div class="min-h-[70vh] flex flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl">

        {{-- ===== SUCCESS HEADER ===== --}}
        <div class="text-center mb-6">
            {{-- Animated checkmark --}}
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4 relative">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <div class="absolute inset-0 rounded-full border-4 border-green-200 animate-ping opacity-30"></div>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Pemesanan Berhasil!</h1>
            <p class="text-slate-500 mt-1 text-sm">Tiket berhasil dipesan. Berikut detail pemesanan Anda.</p>
        </div>

        {{-- ===== MAIN CARD ===== --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">

            {{-- Nomor Pemesanan Banner --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-widest mb-0.5">Nomor Pemesanan</p>
                    <p class="text-white text-2xl font-extrabold tracking-wide">#{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($pemesanan->status_pembayaran === 'lunas')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-400/20 text-green-100 border border-green-400/30 rounded-full text-sm font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></path></svg>
                            Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400/20 text-amber-100 border border-amber-400/30 rounded-full text-sm font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></path></svg>
                            Pending
                        </span>
                    @endif
                    @if($pemesanan->is_round_trip)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 text-white border border-white/30 rounded-full text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Pulang-Pergi
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- ===== DATA PEMESAN ===== --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Nama Pemesan</p>
                        <p class="font-semibold text-slate-800">{{ $pemesanan->nama_pemesan }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Nomor HP</p>
                        <p class="font-semibold text-slate-800">{{ $pemesanan->no_hp_pemesan }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Tanggal Transaksi</p>
                        <p class="font-semibold text-slate-800">{{ $pemesanan->tanggal_transaksi->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Metode Pembayaran</p>
                        <div class="flex items-center gap-2">
                            @if($pemesanan->metode_pembayaran === 'Cash')
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif($pemesanan->metode_pembayaran === 'Transfer')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @endif
                            <span class="font-semibold text-slate-800">{{ $pemesanan->metode_pembayaran }}</span>
                        </div>
                    </div>
                </div>

                {{-- ===== TIKET PERGI ===== --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-blue-50 px-4 py-3 border-b border-slate-200 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                        <h3 class="font-semibold text-blue-800 text-sm">Tiket Keberangkatan</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        {{-- Rute & Info Bus --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-slate-800">{{ $pemesanan->jadwal->rute->asal }}</span>
                                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    <span class="text-lg font-bold text-slate-800">{{ $pemesanan->jadwal->rute->tujuan }}</span>
                                </div>
                                <div class="flex flex-wrap gap-3 mt-2 text-sm text-slate-600">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $pemesanan->jadwal->tanggal_berangkat->format('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ \Illuminate\Support\Str::substr($pemesanan->jadwal->waktu_berangkat, 0, 5) }} WIB
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        {{ $pemesanan->jadwal->bus->tipe_bus }} · {{ $pemesanan->jadwal->bus->nomor_polisi }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-500">Harga/kursi</p>
                                <p class="font-bold text-blue-700">Rp {{ number_format($pemesanan->jadwal->harga_tiket, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Daftar Penumpang Pergi --}}
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Penumpang</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($pemesanan->penumpangsPergi as $penumpang)
                                    <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                        <div class="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ $penumpang->nomor_kursi }}
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Kursi {{ $penumpang->nomor_kursi }}</p>
                                            <p class="font-semibold text-slate-800 text-sm">{{ $penumpang->nama_penumpang }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Subtotal Pergi --}}
                        <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                            <span class="text-sm text-slate-600">Subtotal Tiket Pergi ({{ $pemesanan->penumpangsPergi->count() }} kursi)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($pemesanan->jadwal->harga_tiket * $pemesanan->penumpangsPergi->count(), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- ===== TIKET PULANG (jika round trip) ===== --}}
                @if($pemesanan->is_round_trip && $pemesanan->jadwalPulang)
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-green-50 px-4 py-3 border-b border-slate-200 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                            </svg>
                            <h3 class="font-semibold text-green-800 text-sm">Tiket Pulang</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-slate-800">{{ $pemesanan->jadwalPulang->rute->asal }}</span>
                                        <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        <span class="text-lg font-bold text-slate-800">{{ $pemesanan->jadwalPulang->rute->tujuan }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-slate-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $pemesanan->jadwalPulang->tanggal_berangkat->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ \Illuminate\Support\Str::substr($pemesanan->jadwalPulang->waktu_berangkat, 0, 5) }} WIB
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            {{ $pemesanan->jadwalPulang->bus->tipe_bus }} · {{ $pemesanan->jadwalPulang->bus->nomor_polisi }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga/kursi</p>
                                    <p class="font-bold text-green-700">Rp {{ number_format($pemesanan->jadwalPulang->harga_tiket, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Penumpang</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($pemesanan->penumpangsPulang as $penumpang)
                                        <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                            <div class="w-9 h-9 bg-green-600 text-white rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ $penumpang->nomor_kursi }}
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Kursi {{ $penumpang->nomor_kursi }}</p>
                                                <p class="font-semibold text-slate-800 text-sm">{{ $penumpang->nama_penumpang }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                <span class="text-sm text-slate-600">Subtotal Tiket Pulang ({{ $pemesanan->penumpangsPulang->count() }} kursi)</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($pemesanan->jadwalPulang->harga_tiket * $pemesanan->penumpangsPulang->count(), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ===== TOTAL BAYAR ===== --}}
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Total Pembayaran</p>
                            @if($pemesanan->is_round_trip)
                                <p class="text-xs text-blue-600 mt-0.5">Termasuk tiket pergi & pulang</p>
                            @endif
                        </div>
                        <p class="text-2xl font-extrabold text-blue-800">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Status info untuk pending --}}
                @if($pemesanan->status_pembayaran === 'pending')
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-amber-800 text-sm">Menunggu Konfirmasi Pembayaran</p>
                            <p class="text-amber-700 text-xs mt-1">
                                Pembayaran via {{ $pemesanan->metode_pembayaran }} perlu dikonfirmasi. Silakan lakukan pembayaran dan konfirmasi di halaman Transaksi.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ===== FOOTER ACTIONS ===== --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a
                    href="{{ route('sales.pemesanan.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pemesanan Baru
                </a>
                <a
                    href="{{ route('sales.transaksi.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lihat Transaksi
                </a>
            </div>
        </div>

        {{-- Pesan kecil di bawah --}}
        <p class="text-center text-xs text-slate-400 mt-4">
            Tiket ini dicatat atas nama <span class="font-medium text-slate-600">{{ auth()->user()->name }}</span> pada {{ now()->format('d M Y, H:i') }} WIB
        </p>
    </div>
</div>

@endsection
