@extends('layouts.user')
@section('title', 'Pemesanan Berhasil — BusTicket')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-12">
        {{-- Status Pemesanan --}}
        <div class="text-center mb-8">

            @if ($pemesanan->status_pembayaran == 'pending')
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">

                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </div>

                <h1 class="text-2xl font-extrabold text-slate-800">
                    Pesanan Berhasil Dibuat
                </h1>

                <p class="text-slate-500 mt-2">
                    Terima kasih, {{ $pemesanan->nama_pemesan }}.
                    Silakan selesaikan pembayaran agar tiket dapat dikonfirmasi.
                </p>
            @else
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">

                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />

                    </svg>

                </div>

                <h1 class="text-2xl font-extrabold text-slate-800">
                    Pembayaran Berhasil!
                </h1>

                <p class="text-slate-500 mt-2">
                    Tiket Anda telah dikonfirmasi dan siap digunakan.
                </p>
            @endif

        </div>

        {{-- Countdown Pembayaran --}}
        @if ($pemesanan->status_pembayaran == 'pending')
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-2xl p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-semibold text-yellow-700">
                            ⏳ Selesaikan pembayaran sebelum
                        </p>

                        <p id="countdown" class="text-3xl font-bold text-red-600">
                            02:00:00
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            Setelah waktu habis, pemesanan akan otomatis dibatalkan.
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-slate-500">
                            Batas pembayaran
                        </p>

                        <p class="font-semibold">
                            {{ $pemesanan->created_at->addHours(2)->format('d M Y H:i') }}
                        </p>
                    </div>

                </div>

            </div>
        @endif
        {{-- Booking Card --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden mb-4">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between">
                <div>
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Kode Pemesanan</p>
                    <p class="text-white text-2xl font-extrabold tracking-widest">
                        #{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                @if ($pemesanan->status_pembayaran == 'pending')
                    <span class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-xl text-sm font-bold">
                        MENUNGGU PEMBAYARAN
                    </span>
                @elseif($pemesanan->status_pembayaran == 'lunas')
                    <span class="bg-green-400 text-green-900 px-4 py-2 rounded-xl text-sm font-bold">
                        PEMBAYARAN BERHASIL
                    </span>
                @else
                    <span class="bg-red-400 text-red-900 px-4 py-2 rounded-xl text-sm font-bold">
                        EXPIRED
                    </span>
                @endif
            </div>

            {{-- Journey Info --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-slate-800">{{ $pemesanan->jadwal->rute->asal }}</p>
                        <p class="text-sm text-slate-500">{{ substr($pemesanan->jadwal->waktu_berangkat, 0, 5) }}</p>
                    </div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="h-px flex-1 border-t-2 border-dashed border-slate-300"></div>
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                        <div class="h-px flex-1 border-t-2 border-dashed border-slate-300"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-slate-800">{{ $pemesanan->jadwal->rute->tujuan }}</p>
                        <p class="text-sm text-slate-500">Tujuan</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-5 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs font-medium uppercase">Tanggal</p>
                        <p class="font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-medium uppercase">Bus</p>
                        <p class="font-semibold text-slate-700">{{ $pemesanan->jadwal->bus->nomor_polisi }}</p>
                        <p class="text-xs text-slate-400">{{ $pemesanan->jadwal->bus->tipe_bus }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-medium uppercase">Pool</p>
                        <p class="font-semibold text-slate-700">{{ $pemesanan->jadwal->pool->nama_pool }}</p>
                    </div>
                </div>
            </div>

            {{-- Passengers --}}
            <div class="p-6 border-b border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Penumpang
                    @if ($pemesanan->is_round_trip)
                        <span class="text-blue-500 ml-1">(Tiket Pergi)</span>
                    @endif
                </p>
                @php $penumpangPergi = $pemesanan->penumpangs->where('jadwal_id', $pemesanan->jadwal_id); @endphp
                <div class="space-y-2">
                    @foreach ($penumpangPergi as $p)
                        <div class="flex items-center gap-3">
                            <span
                                class="font-mono font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg text-sm">{{ $p->nomor_kursi }}</span>
                            <span class="text-slate-700">{{ $p->nama_penumpang }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($pemesanan->is_round_trip && $pemesanan->jadwalPulang)
                    @php $penumpangPulang = $pemesanan->penumpangs->where('jadwal_id', $pemesanan->jadwal_pulang_id); @endphp
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Penumpang (Pulang:
                            {{ $pemesanan->jadwalPulang->rute->asal }} → {{ $pemesanan->jadwalPulang->rute->tujuan }},
                            {{ \Carbon\Carbon::parse($pemesanan->jadwalPulang->tanggal_berangkat)->isoFormat('D MMM') }})
                        </p>
                        <div class="space-y-2">
                            @foreach ($penumpangPulang as $p)
                                <div class="flex items-center gap-3">
                                    <span
                                        class="font-mono font-bold text-purple-700 bg-purple-100 px-2.5 py-1 rounded-lg text-sm">{{ $p->nomor_kursi }}</span>
                                    <span class="text-slate-700">{{ $p->nama_penumpang }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Payment --}}
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 font-medium uppercase">Metode Pembayaran</p>
                        <p class="font-semibold text-slate-700">{{ $pemesanan->metode_pembayaran }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 font-medium uppercase">Total Bayar</p>
                        <p class="text-2xl font-extrabold text-green-600">Rp
                            {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</p>
                    </div>
                </div>
                @if ($pemesanan->status_pembayaran === 'pending')
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
                        <strong>Status: Menunggu Pembayaran.</strong>
                        Segera lakukan transfer/pembayaran sesuai instruksi yang diberikan pool, lalu konfirmasi ke petugas.
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('user.etiket', $pemesanan->id) }}"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3.5 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                Lihat E-Ticket
            </a>
            <a href="{{ route('user.riwayat') }}"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-center font-semibold py-3.5 rounded-xl text-sm transition-colors">
                Riwayat Pemesanan
            </a>
            <a href="{{ route('user.home') }}"
                class="flex-1 border border-slate-200 hover:bg-slate-50 text-slate-600 text-center font-medium py-3.5 rounded-xl text-sm transition-colors">
                Pesan Tiket Baru
            </a>
        </div>
    </div>

    @if ($pemesanan->status_pembayaran == 'pending')
        <script>
            const deadline = {{ $pemesanan->created_at->addHours(2)->timestamp * 1000 }};

            function updateCountdown() {

                const now = Date.now();

                let distance = deadline - now;

                if (distance <= 0) {

                    document.getElementById("countdown").innerHTML = "00:00:00";

                    clearInterval(timer);

                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML =
                    String(hours).padStart(2, '0') + ":" +
                    String(minutes).padStart(2, '0') + ":" +
                    String(seconds).padStart(2, '0');

            }

            updateCountdown();

            const timer = setInterval(updateCountdown, 1000);
        </script>
    @endif
@endsection
