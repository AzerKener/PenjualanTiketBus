@extends('layouts.user')
@section('title', 'Pesan Tiket — ' . $jadwal->rute->asal . ' → ' . $jadwal->rute->tujuan)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data='pemesanan(@json($semuaKursi), @json($kursiTerisi), {{ $jadwal->id }}, {{ $jadwal->harga_tiket }})'>

    {{-- Progress Steps --}}
    <div class="flex items-center gap-2 mb-8">
        <div class="flex items-center gap-2">
            <div :class="step === 1 ? 'bg-blue-600 text-white' : 'bg-green-500 text-white'"
                 class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors">
                <span x-show="step === 1">1</span>
                <svg x-show="step > 1" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span :class="step === 1 ? 'text-blue-700 font-semibold' : 'text-green-600 font-semibold'"
                  class="text-sm hidden sm:block">Pilih Kursi & Penumpang</span>
        </div>
        <div class="flex-1 h-px" :class="step > 1 ? 'bg-green-400' : 'bg-slate-200'"></div>
        <div class="flex items-center gap-2">
            <div :class="step === 2 ? 'bg-blue-600 text-white' : (step > 2 ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-500')"
                 class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors">2</div>
            <span :class="step === 2 ? 'text-blue-700 font-semibold' : 'text-slate-400'"
                  class="text-sm hidden sm:block">Konfirmasi & Bayar</span>
        </div>
    </div>

    {{-- Info Jadwal --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl p-5 mb-6 shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-blue-200 text-xs font-medium uppercase tracking-wider mb-1">Rute Keberangkatan</p>
                <h1 class="text-2xl font-extrabold">{{ $jadwal->rute->asal }} → {{ $jadwal->rute->tujuan }}</h1>
                <p class="text-blue-200 mt-1 text-sm">
                    {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('dddd, D MMMM Y') }}
                    • {{ substr($jadwal->waktu_berangkat, 0, 5) }} WIB
                </p>
            </div>
            <div class="text-right">
                <p class="text-blue-200 text-sm">{{ $jadwal->bus->nomor_polisi }} ({{ $jadwal->bus->tipe_bus }})</p>
                <p class="text-2xl font-extrabold">Rp {{ number_format($jadwal->harga_tiket, 0, ',', '.') }}</p>
                <p class="text-blue-200 text-xs">per kursi</p>
            </div>
        </div>
    </div>

    {{-- ═══ STEP 1: Pilih Kursi + Data Penumpang ═══ --}}
    <div x-show="step === 1">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Kiri: Denah Kursi --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                    Pilih Kursi
                </h2>

                {{-- Legend --}}
                <div class="flex items-center gap-4 mb-4 text-xs text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg border-2 border-slate-300 bg-white"></div>
                        <span>Tersedia</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg bg-blue-600"></div>
                        <span>Dipilih</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg bg-slate-200"></div>
                        <span>Terisi</span>
                    </div>
                </div>

                {{-- Indikator Supir --}}
                <div class="flex justify-end mb-3">
                    <div class="bg-slate-100 rounded-lg px-3 py-1.5 text-xs text-slate-500 font-medium flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Supir
                    </div>
                </div>

                {{-- Seat Grid (2+2 layout) --}}
                <div class="overflow-y-auto max-h-80">
                    <div class="grid gap-2"
                         :style="'grid-template-columns: 1fr 1fr 24px 1fr 1fr'">
                        <template x-for="(kursi, idx) in semuaKursi" :key="kursi">
                            <div class="contents">
                                <template x-if="idx % 4 === 2">
                                    {{-- Lorong --}}
                                    <div></div>
                                </template>
                                <button type="button"
                                    :class="{
                                        'bg-blue-600 text-white border-blue-600 shadow-md': dipilih.includes(kursi),
                                        'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200': terisi.includes(kursi),
                                        'bg-white border-slate-300 hover:border-blue-400 hover:bg-blue-50 text-slate-700 cursor-pointer': !dipilih.includes(kursi) && !terisi.includes(kursi)
                                    }"
                                    class="h-10 rounded-lg border-2 text-xs font-bold transition-all duration-150"
                                    :disabled="terisi.includes(kursi)"
                                    @click="toggleKursi(kursi)"
                                    x-text="kursi">
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Kursi Dipilih Summary --}}
                <div x-show="dipilih.length > 0" x-cloak class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-xs text-blue-600 font-semibold mb-1">Kursi dipilih (<span x-text="dipilih.length"></span>):</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="k in dipilih" :key="'tag-'+k">
                            <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs font-mono font-bold" x-text="k"></span>
                        </template>
                    </div>
                </div>

                {{-- Tiket Pulang-Pergi --}}
                <div class="mt-5 border-t border-slate-100 pt-5">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" x-model="isRoundTrip"
                            class="w-5 h-5 rounded text-blue-600 border-slate-300 mt-0.5 flex-shrink-0">
                        <div>
                            <p class="font-semibold text-slate-700 group-hover:text-blue-600 transition-colors text-sm">Tiket Pulang-Pergi</p>
                            <p class="text-xs text-slate-400 mt-0.5">Tambah perjalanan kembali</p>
                        </div>
                    </label>

                    <div x-show="isRoundTrip" x-cloak class="mt-3 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Jadwal Pulang</label>
                            <select x-model="jadwalPulangId"
                                @change="loadKursiPulang($event.target.value)"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @if($jadwalPulangList->isEmpty())
                                    <option value="" disabled selected>-- Tidak ada jadwal pulang yang tersedia --</option>
                                @else
                                    <option value="">-- Pilih Jadwal Pulang --</option>
                                    @foreach($jadwalPulangList as $jp)
                                    <option value="{{ $jp->id }}"
                                        data-harga="{{ $jp->harga_tiket }}"
                                        data-jumlah="{{ $jp->bus->jumlah_kursi }}">
                                        {{ $jp->rute->tujuan ?? '' }} → {{ $jp->rute->asal ?? '' }} |
                                        {{ \Carbon\Carbon::parse($jp->tanggal_berangkat)->isoFormat('D MMM') }}
                                        {{ substr($jp->waktu_berangkat, 0, 5) }} |
                                        {{ $jp->bus->tipe_bus }} |
                                        Rp {{ number_format($jp->harga_tiket, 0, ',', '.') }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Denah Kursi Pulang --}}
                        <div x-show="jadwalPulangId && semuaKursiPulang.length > 0" x-cloak>
                            <p class="text-xs font-semibold text-purple-600 mb-2">Kursi Pulang</p>
                            <div class="grid gap-1.5 overflow-y-auto max-h-52"
                                 :style="'grid-template-columns: 1fr 1fr 20px 1fr 1fr'">
                                <template x-for="(kursi, idx) in semuaKursiPulang" :key="'p-'+kursi">
                                    <div class="contents">
                                        <template x-if="idx % 4 === 2">
                                            <div></div>
                                        </template>
                                        <button type="button"
                                            :class="{
                                                'bg-purple-600 text-white border-purple-600': dipilihPulang.includes(kursi),
                                                'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200': terisiPulang.includes(kursi),
                                                'bg-white border-slate-300 hover:border-purple-400 hover:bg-purple-50 text-slate-700': !dipilihPulang.includes(kursi) && !terisiPulang.includes(kursi)
                                            }"
                                            class="h-9 rounded-lg border-2 text-xs font-bold transition-all"
                                            :disabled="terisiPulang.includes(kursi)"
                                            @click="toggleKursiPulang(kursi)"
                                            x-text="kursi">
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Form Data Penumpang --}}
            <div class="space-y-4">

                {{-- Penumpang Pergi --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                        <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Data Penumpang
                    </h2>

                    <div x-show="dipilih.length === 0" class="text-sm text-slate-400 text-center py-8 flex flex-col items-center gap-2">
                        <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        <span>Pilih kursi terlebih dahulu</span>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(kursi, i) in dipilih" :key="'form-'+kursi">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     x-text="kursi"></div>
                                <input type="text"
                                    :id="'nama_penumpang_pergi_' + i"
                                    x-model="namaPenumpangPergi[i]"
                                    placeholder="Nama penumpang"
                                    required
                                    class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Penumpang Pulang --}}
                <div x-show="isRoundTrip && dipilihPulang.length > 0" x-cloak
                     class="bg-white rounded-2xl border border-purple-200 shadow-sm p-6">
                    <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                        <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        Data Penumpang
                        <span class="text-xs text-purple-600 font-normal">(pulang)</span>
                    </h2>
                    <div class="space-y-3">
                        <template x-for="(kursi, i) in dipilihPulang" :key="'pform-'+kursi">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-10 bg-purple-600 text-white rounded-xl flex items-center justify-center text-xs font-bold flex-shrink-0"
                                     x-text="kursi"></div>
                                <input type="text"
                                    x-model="namaPenumpangPulang[i]"
                                    placeholder="Nama penumpang"
                                    required
                                    class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Tombol Lanjut ke Pembayaran --}}
                <button type="button"
                    @click="lanjutKePembayaran()"
                    :disabled="!bisaLanjut()"
                    :class="bisaLanjut() ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-slate-300 cursor-not-allowed'"
                    class="w-full text-white font-bold py-4 rounded-2xl text-sm transition-all flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Lanjut ke Pembayaran
                    <template x-if="!bisaLanjut()">
                        <span class="text-xs font-normal">(pilih kursi & isi nama)</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ STEP 2: Konfirmasi & Pembayaran ═══ --}}
    <div x-show="step === 2" x-cloak>
        <form method="POST" action="{{ route('user.pesan.store') }}" id="formPembayaran">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <input type="hidden" name="is_round_trip" :value="isRoundTrip ? 1 : 0">
            <input type="hidden" name="jadwal_pulang_id" :value="jadwalPulangId">

            {{-- Hidden: kursi + nama pergi --}}
            <template x-for="(kursi, i) in dipilih" :key="'h-pergi-'+i">
                <input type="hidden" :name="'kursi_pergi[' + i + ']'" :value="kursi">
            </template>
            <template x-for="(nama, i) in namaPenumpangPergi" :key="'hn-pergi-'+i">
                <input type="hidden" :name="'nama_penumpang_pergi[' + i + ']'" :value="nama">
            </template>

            {{-- Hidden: kursi + nama pulang --}}
            <template x-for="(kursi, i) in dipilihPulang" :key="'h-pulang-'+i">
                <input type="hidden" :name="'kursi_pulang[' + i + ']'" :value="kursi">
            </template>
            <template x-for="(nama, i) in namaPenumpangPulang" :key="'hn-pulang-'+i">
                <input type="hidden" :name="'nama_penumpang_pulang[' + i + ']'" :value="nama">
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kiri: Ringkasan Pesanan --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Detail Perjalanan --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h2 class="font-bold text-slate-800 mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Ringkasan Pesanan
                        </h2>

                        {{-- Tiket Pergi --}}
                        <div class="rounded-xl border border-slate-200 overflow-hidden mb-3">
                            <div class="bg-blue-50 px-4 py-2.5 flex items-center justify-between">
                                <span class="text-sm font-semibold text-blue-700">
                                    {{ $jadwal->rute->asal }} → {{ $jadwal->rute->tujuan }}
                                </span>
                                <span class="text-xs text-blue-500">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}
                                    {{ substr($jadwal->waktu_berangkat, 0, 5) }}
                                </span>
                            </div>
                            <div class="px-4 py-3 space-y-2">
                                <template x-for="(kursi, i) in dipilih" :key="'sum-'+kursi">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-slate-700 bg-blue-50 px-2 py-0.5 rounded text-xs"
                                                  x-text="kursi"></span>
                                            <span class="text-slate-600" x-text="namaPenumpangPergi[i] || '—'"></span>
                                        </div>
                                        <span class="text-slate-700 font-medium">Rp {{ number_format($jadwal->harga_tiket, 0, ',', '.') }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Tiket Pulang --}}
                        <div x-show="isRoundTrip && dipilihPulang.length > 0" x-cloak
                             class="rounded-xl border border-purple-200 overflow-hidden">
                            <div class="bg-purple-50 px-4 py-2.5 flex items-center justify-between">
                                <span class="text-sm font-semibold text-purple-700">
                                    {{ $jadwal->rute->tujuan }} → {{ $jadwal->rute->asal }}
                                    <span class="text-purple-400 font-normal text-xs">(pulang)</span>
                                </span>
                            </div>
                            <div class="px-4 py-3 space-y-2">
                                <template x-for="(kursi, i) in dipilihPulang" :key="'psum-'+kursi">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded text-xs"
                                                  x-text="kursi"></span>
                                            <span class="text-slate-600" x-text="namaPenumpangPulang[i] || '—'"></span>
                                        </div>
                                        <span class="text-slate-700 font-medium"
                                              x-text="'Rp ' + hargaPulang.toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Metode Pembayaran
                        </h2>

                        <div class="space-y-3">
                            {{-- CASH --}}
                            <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="metodePembayaran === 'Cash' ? 'border-green-500 bg-green-50' : 'border-slate-200 hover:border-green-300 hover:bg-green-50/50'">
                                <input type="radio" name="metode_pembayaran" value="Cash"
                                       x-model="metodePembayaran" required class="mt-1 text-green-600 w-4 h-4 flex-shrink-0">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">💵</span>
                                        <span class="font-semibold text-slate-800">Tunai (Cash)</span>
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Langsung Lunas</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Bayar langsung di pool keberangkatan sebelum bus berangkat.</p>
                                </div>
                            </label>

                            {{-- TRANSFER --}}
                            <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="metodePembayaran === 'Transfer' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-blue-300 hover:bg-blue-50/50'">
                                <input type="radio" name="metode_pembayaran" value="Transfer"
                                       x-model="metodePembayaran" required class="mt-1 text-blue-600 w-4 h-4 flex-shrink-0">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">🏦</span>
                                        <span class="font-semibold text-slate-800">Transfer Bank</span>
                                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Perlu Konfirmasi</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Transfer ke rekening BusTicket, lalu konfirmasi ke petugas pool.</p>
                                    {{-- Instruksi Transfer --}}
                                    <div x-show="metodePembayaran === 'Transfer'" x-cloak
                                         class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
                                        <p class="text-xs font-semibold text-blue-700 mb-2">Rekening Transfer:</p>
                                        <div class="space-y-1.5 text-xs text-blue-800">
                                            <div class="flex items-center justify-between">
                                                <span>Bank BCA</span>
                                                <span class="font-mono font-bold text-sm">1234 5678 90</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>Bank Mandiri</span>
                                                <span class="font-mono font-bold text-sm">1100 0099 8877</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>a.n.</span>
                                                <span class="font-semibold">PT BusTicket Indonesia</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-blue-600 mt-2 font-medium">
                                            ⚠️ Sertakan nomor HP Anda sebagai berita transfer.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            {{-- E-WALLET --}}
                            <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="metodePembayaran === 'E-Wallet' ? 'border-purple-500 bg-purple-50' : 'border-slate-200 hover:border-purple-300 hover:bg-purple-50/50'">
                                <input type="radio" name="metode_pembayaran" value="E-Wallet"
                                       x-model="metodePembayaran" required class="mt-1 text-purple-600 w-4 h-4 flex-shrink-0">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">📱</span>
                                        <span class="font-semibold text-slate-800">E-Wallet</span>
                                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Perlu Konfirmasi</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">GoPay, OVO, DANA, ShopeePay — transfer ke nomor berikut.</p>
                                    {{-- Instruksi E-Wallet --}}
                                    <div x-show="metodePembayaran === 'E-Wallet'" x-cloak
                                         class="mt-3 p-3 bg-purple-50 rounded-xl border border-purple-200">
                                        <p class="text-xs font-semibold text-purple-700 mb-2">Nomor E-Wallet:</p>
                                        <div class="space-y-1.5 text-xs text-purple-800">
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center gap-1">🟢 GoPay / DANA</span>
                                                <span class="font-mono font-bold text-sm">0812-0000-0001</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center gap-1">🔵 OVO</span>
                                                <span class="font-mono font-bold text-sm">0812-0000-0002</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center gap-1">🟠 ShopeePay</span>
                                                <span class="font-mono font-bold text-sm">0812-0000-0003</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>a.n.</span>
                                                <span class="font-semibold">BusTicket Official</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-purple-600 mt-2 font-medium">
                                            ⚠️ Screenshot bukti transfer & kirim ke WA: 0812-9999-0000
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Total & Submit --}}
                <div class="space-y-4">

                    {{-- Order Summary --}}
                    <div class="bg-slate-800 text-white rounded-2xl p-6 sticky top-24">
                        <h3 class="font-bold mb-4 text-base">Total Pembayaran</h3>

                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex justify-between text-slate-300">
                                <span>Tiket pergi</span>
                                <span x-text="dipilih.length + ' × Rp ' + hargaPergi.toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between text-slate-300 font-medium">
                                <span></span>
                                <span x-text="'Rp ' + (dipilih.length * hargaPergi).toLocaleString('id-ID')"></span>
                            </div>

                            <div x-show="isRoundTrip && dipilihPulang.length > 0" x-cloak class="border-t border-slate-700 pt-2 mt-2">
                                <div class="flex justify-between text-slate-300">
                                    <span>Tiket pulang</span>
                                    <span x-text="dipilihPulang.length + ' × Rp ' + hargaPulang.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-slate-300 font-medium">
                                    <span></span>
                                    <span x-text="'Rp ' + (dipilihPulang.length * hargaPulang).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-700 pt-4 mb-5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300 text-sm">Grand Total </span>
                                <span class="text-2xl font-extrabold text-green-400"
                                      x-text="' Rp ' + totalHarga().toLocaleString('id-ID')">
                                </span>
                            </div>
                            <p x-show="metodePembayaran === 'Cash'" x-cloak
                               class="text-xs text-green-400 mt-1">✓ Status: Langsung Lunas</p>
                            <p x-show="metodePembayaran && metodePembayaran !== 'Cash'" x-cloak
                               class="text-xs text-amber-400 mt-1">⏳ Status: Pending konfirmasi</p>
                        </div>

                        <button type="submit"
                            :disabled="!metodePembayaran"
                            :class="metodePembayaran ? 'bg-green-500 hover:bg-green-400' : 'bg-slate-600 cursor-not-allowed'"
                            class="w-full text-white font-bold py-4 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="metodePembayaran ? 'Konfirmasi Pemesanan' : 'Pilih Metode Bayar'"></span>
                        </button>

                        <button type="button" @click="step = 1"
                            class="w-full mt-2 text-slate-400 hover:text-white text-xs py-2 transition-colors flex items-center justify-center gap-1">
                            ← Kembali ubah kursi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
function pemesanan(semuaKursi, kursiTerisi, jadwalId, hargaPergi) {
    return {
        step: 1,
        semuaKursi,
        terisi: kursiTerisi,
        dipilih: [],
        namaPenumpangPergi: [],
        isRoundTrip: false,
        jadwalPulangId: '',
        hargaPergi,
        hargaPulang: 0,
        semuaKursiPulang: [],
        terisiPulang: [],
        dipilihPulang: [],
        namaPenumpangPulang: [],
        metodePembayaran: '',

        toggleKursi(kursi) {
            if (this.terisi.includes(kursi)) return;
            const idx = this.dipilih.indexOf(kursi);
            if (idx !== -1) {
                this.dipilih.splice(idx, 1);
                this.namaPenumpangPergi.splice(idx, 1);
            } else {
                this.dipilih.push(kursi);
                this.namaPenumpangPergi.push('');
            }
        },

        toggleKursiPulang(kursi) {
            if (this.terisiPulang.includes(kursi)) return;
            const idx = this.dipilihPulang.indexOf(kursi);
            if (idx !== -1) {
                this.dipilihPulang.splice(idx, 1);
                this.namaPenumpangPulang.splice(idx, 1);
            } else {
                this.dipilihPulang.push(kursi);
                this.namaPenumpangPulang.push('');
            }
        },

        async loadKursiPulang(jadwalPId) {
            this.semuaKursiPulang = [];
            this.terisiPulang = [];
            this.dipilihPulang = [];
            this.namaPenumpangPulang = [];
            this.hargaPulang = 0;
            if (!jadwalPId) return;

            const resp = await fetch(`/tiket/kursi-terisi/${jadwalPId}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
            const data = await resp.json();
            this.terisiPulang = data.kursi_terisi;

            const jumlah = data.jumlah_kursi;
            const cols = ['A','B','C','D'];
            const seats = [];
            for (let r = 1; r <= Math.ceil(jumlah / 4); r++) {
                for (let c of cols) {
                    if (seats.length < jumlah) seats.push(r + c);
                }
            }
            this.semuaKursiPulang = seats;

            // Ambil harga dari select option
            const sel = document.querySelector('[x-model="jadwalPulangId"]');
            if (sel) {
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.dataset.harga) {
                    this.hargaPulang = parseInt(opt.dataset.harga);
                }
            }
        },

        bisaLanjut() {
            if (this.dipilih.length === 0) return false;
            // Cek semua nama terisi
            for (let i = 0; i < this.dipilih.length; i++) {
                if (!this.namaPenumpangPergi[i] || this.namaPenumpangPergi[i].trim() === '') return false;
            }
            // Jika round trip, cek kursi pulang dipilih
            if (this.isRoundTrip) {
                if (!this.jadwalPulangId) return false;
                if (this.dipilihPulang.length === 0) return false;
                for (let i = 0; i < this.dipilihPulang.length; i++) {
                    if (!this.namaPenumpangPulang[i] || this.namaPenumpangPulang[i].trim() === '') return false;
                }
            }
            return true;
        },

        lanjutKePembayaran() {
            if (!this.bisaLanjut()) return;
            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        totalHarga() {
            const pergi = this.dipilih.length * this.hargaPergi;
            const pulang = (this.isRoundTrip && this.dipilihPulang.length > 0)
                ? this.dipilihPulang.length * this.hargaPulang : 0;
            return pergi + pulang;
        }
    }
}
</script>
@endpush
@endsection
