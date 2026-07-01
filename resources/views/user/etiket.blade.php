@extends('layouts.user')

@section('title', 'E-Ticket #' . str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) . ' — BusTicket')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-4 print:hidden">
            <a href="{{ route('user.riwayat') }}"
                class="flex items-center gap-1.5 text-sm text-slate-600 hover:text-blue-600">
                ← Riwayat Pemesanan
            </a>
            <button onclick="window.print()"
                class="flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>

        {{-- E-Ticket --}}
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-xl overflow-hidden" id="eticket-content">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-700 to-blue-600 px-8 py-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-lg">BusTicket</p>
                        <p class="text-blue-200 text-xs">E-Tiket Resmi</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-blue-200 text-xs font-medium">KODE TIKET</p>
                    <p class="text-white text-2xl font-extrabold tracking-widest">
                        #{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            {{-- Perforasi decoratif --}}
            <div class="flex items-center px-4">
                <div class="w-5 h-5 bg-slate-100 rounded-full -ml-5 border-2 border-slate-200"></div>
                <div class="flex-1 border-t-2 border-dashed border-slate-200 mx-2"></div>
                <div class="w-5 h-5 bg-slate-100 rounded-full -mr-5 border-2 border-slate-200"></div>
            </div>

            {{-- Journey --}}
            <div class="px-8 py-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-3xl font-extrabold text-slate-800">{{ $pemesanan->jadwal->rute->asal }}</p>
                        <p class="text-sm text-slate-500 font-semibold mt-0.5">
                            {{ substr($pemesanan->jadwal->waktu_berangkat, 0, 5) }} WIB</p>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-0.5">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        @if ($pemesanan->is_round_trip)
                            <span
                                class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded">PERGI-PULANG</span>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-extrabold text-slate-800">{{ $pemesanan->jadwal->rute->tujuan }}</p>
                        <p class="text-sm text-slate-500 font-semibold mt-0.5">Tujuan</p>
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    @if($pemesanan->jadwal->status === 'menunggu')
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold tracking-wide">BUS MENUNGGU</span>
                    @elseif($pemesanan->jadwal->status === 'boarding')
                        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide animate-pulse">PROSES BOARDING - SILAKAN NAIK</span>
                    @elseif($pemesanan->jadwal->status === 'berangkat')
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide animate-pulse">BUS BERANGKAT</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">TELAH TIBA DI TUJUAN</span>
                    @endif
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Tanggal</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">
                            {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_berangkat)->isoFormat('D MMMM Y') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Bus</p>
                        <p class="font-bold text-slate-700 font-mono mt-0.5 text-sm">
                            {{ $pemesanan->jadwal->bus->nomor_polisi }}</p>
                        <p class="text-xs text-slate-400">{{ $pemesanan->jadwal->bus->tipe_bus }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Pool Keberangkatan</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->jadwal->pool->nama_pool }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Pool Kedatangan</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->jadwal->poolTujuan ? $pemesanan->jadwal->poolTujuan->nama_pool : '-' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Nama Pemesan</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->nama_pemesan }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Metode Bayar</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->metode_pembayaran }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Status</p>
                        @if ($pemesanan->status_pembayaran === 'lunas')
                            <span
                                class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">✓
                                LUNAS</span>
                        @else
                            <span
                                class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">PENDING</span>
                        @endif
                    </div>
                    @if($pemesanan->jadwal->supir1)
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Supir 1</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->jadwal->supir1->nama }}</p>
                    </div>
                    @endif
                    @if($pemesanan->jadwal->supir2)
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Supir 2</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->jadwal->supir2->nama }}</p>
                    </div>
                    @endif
                    @if($pemesanan->jadwal->kenek)
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium uppercase">Kenek</p>
                        <p class="font-bold text-slate-700 mt-0.5 text-sm">{{ $pemesanan->jadwal->kenek->nama }}</p>
                    </div>
                    @endif
                </div>

                {{-- Lokasi Pool (Google Maps) --}}
                @if($pemesanan->jadwal->pool->latitude && $pemesanan->jadwal->pool->longitude)
                <div class="mb-6 print:hidden">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lokasi Pool Keberangkatan</p>
                    <div class="rounded-xl overflow-hidden border border-slate-200">
                        <iframe 
                            width="100%" 
                            height="150" 
                            frameborder="0" 
                            style="border:0" 
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://maps.google.com/maps?q={{ $pemesanan->jadwal->pool->latitude }},{{ $pemesanan->jadwal->pool->longitude }}&hl=id&z=15&output=embed" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                @endif

                {{-- Passengers (Pergi) --}}
                @php $penumpangPergi = $pemesanan->penumpangs->where('jadwal_id', $pemesanan->jadwal_id); @endphp
                <div class="border border-slate-200 rounded-2xl overflow-hidden mb-4">
                    <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Kursi & Penumpang
                            (Pergi)</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($penumpangPergi as $pnp)
                            <div class="flex items-center gap-3 px-4 py-2.5">
                                <span
                                    class="font-mono font-extrabold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg text-sm">{{ $pnp->nomor_kursi }}</span>
                                <span class="text-slate-700 font-medium text-sm">{{ $pnp->nama_penumpang }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Passengers (Pulang) --}}
                @if ($pemesanan->is_round_trip && $pemesanan->jadwalPulang)
                    @php $penumpangPulang = $pemesanan->penumpangs->where('jadwal_id', $pemesanan->jadwal_pulang_id); @endphp
                    <div class="border border-purple-200 rounded-2xl overflow-hidden mb-4">
                        <div class="bg-purple-50 px-4 py-2.5 border-b border-purple-200">
                            <p class="text-xs font-bold text-purple-500 uppercase tracking-wider">
                                Perjalanan Pulang: {{ $pemesanan->jadwalPulang->rute->asal }} →
                                {{ $pemesanan->jadwalPulang->rute->tujuan }}
                                |
                                {{ \Carbon\Carbon::parse($pemesanan->jadwalPulang->tanggal_berangkat)->isoFormat('D MMM Y') }}
                                {{ substr($pemesanan->jadwalPulang->waktu_berangkat, 0, 5) }}
                            </p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($penumpangPulang as $pnp)
                                <div class="flex items-center gap-3 px-4 py-2.5">
                                    <span
                                        class="font-mono font-extrabold text-purple-700 bg-purple-50 px-3 py-1 rounded-lg text-sm">{{ $pnp->nomor_kursi }}</span>
                                    <span class="text-slate-700 font-medium text-sm">{{ $pnp->nama_penumpang }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Total --}}
                <div
                    class="bg-gradient-to-r from-slate-800 to-slate-700 text-white rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs">Total Pembayaran</p>
                        <p class="text-2xl font-extrabold">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right text-xs text-slate-400">
                        <p>Tanggal Pesan:</p>
                        <p class="text-white font-semibold">
                            {{ \Carbon\Carbon::parse($pemesanan->tanggal_transaksi)->isoFormat('D MMM Y HH:mm') }}</p>
                    </div>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="px-8 py-6 text-center">
                <div id="qrcode" class="inline-block"></div>
                <p class="text-xs text-slate-400 mt-2">Scan untuk verifikasi tiket</p>
            </div>

            {{-- Footer --}}
            <div class="border-t border-slate-200 px-8 py-4 bg-slate-50 text-center">
                <p class="text-xs text-slate-400">Harap tiba di pool keberangkatan minimal 30 menit sebelum jadwal.
                    Tunjukkan e-tiket ini kepada petugas.</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {

                nav,
                footer,
                .print\:hidden {
                    display: none !important;
                }

                body {
                    background: white;
                }

                #eticket-content {
                    box-shadow: none;
                    border: 1px solid #ccc;
                }

                #qrcode {
                    display: block !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new QRCode(document.getElementById('qrcode'), {
                text: '{{ route('user.etiket', $pemesanan->id) }}',
                width: 120,
                height: 120,
                colorDark: '#1e293b',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    </script>
    @endpush
@endsection
