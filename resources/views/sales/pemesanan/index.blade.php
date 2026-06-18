@extends('layouts.sales')
@section('title', 'Pemesanan Tiket')
@section('content')

<div
    x-data="pemesananWizard()"
    x-init="init()"
    class="space-y-6"
>
    {{-- ===== HEADER ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pemesanan Tiket</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cari jadwal, pilih kursi, dan proses pembayaran</p>
        </div>
        {{-- Step indicator --}}
        <div class="hidden sm:flex items-center gap-2 text-xs font-medium">
            <div :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'" class="w-7 h-7 rounded-full flex items-center justify-center transition-colors">1</div>
            <div :class="step >= 1 ? 'bg-blue-300' : 'bg-slate-200'" class="w-8 h-0.5 transition-colors"></div>
            <div :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'" class="w-7 h-7 rounded-full flex items-center justify-center transition-colors">2</div>
            <div :class="step >= 2 ? 'bg-blue-300' : 'bg-slate-200'" class="w-8 h-0.5 transition-colors"></div>
            <div :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'" class="w-7 h-7 rounded-full flex items-center justify-center transition-colors">3</div>
        </div>
    </div>

    {{-- ===== STEP 1: PENCARIAN JADWAL ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-slate-800">Langkah 1 — Cari Jadwal</h2>
                <p class="text-xs text-slate-500">Isi rute dan tanggal keberangkatan</p>
            </div>
        </div>

        <div class="p-5">
            <form @submit.prevent="cariJadwal()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Asal --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Kota Asal</label>
                    <select
                        x-model="search.asal"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                        required
                    >
                        <option value="">-- Pilih Asal --</option>
                        @foreach($asalList as $asal)
                            <option value="{{ $asal }}">{{ $asal }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tujuan --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Kota Tujuan</label>
                    <select
                        x-model="search.tujuan"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                        required
                    >
                        <option value="">-- Pilih Tujuan --</option>
                        @foreach($tujuanList as $tujuan)
                            <option value="{{ $tujuan }}">{{ $tujuan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tanggal Berangkat</label>
                    <input
                        type="date"
                        x-model="search.tanggal"
                        :min="today"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- Tipe Bus --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tipe Bus</label>
                    <select
                        x-model="search.tipe_bus"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    >
                        <option value="">Semua Tipe</option>
                        @foreach($tipesBus as $tipe)
                            <option value="{{ $tipe }}">{{ $tipe }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Button --}}
                <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                    <button
                        type="submit"
                        :disabled="loadingSearch"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
                    >
                        <svg x-show="!loadingSearch" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <svg x-show="loadingSearch" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <span x-text="loadingSearch ? 'Mencari...' : 'Cari Jadwal'"></span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Hasil Pencarian --}}
        <div x-show="jadwalList.length > 0 || searchDone" x-cloak class="border-t border-slate-100">
            <div class="px-5 py-3 bg-slate-50 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-700">
                    <span x-text="jadwalList.length"></span> jadwal ditemukan
                </span>
                <span class="text-xs text-slate-500" x-text="search.asal + ' → ' + search.tujuan + ' · ' + formatTanggal(search.tanggal)"></span>
            </div>

            {{-- Empty state --}}
            <div x-show="searchDone && jadwalList.length === 0" class="p-8 text-center">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-slate-500 font-medium">Tidak ada jadwal tersedia</p>
                <p class="text-slate-400 text-sm mt-1">Coba ubah rute atau tanggal pencarian</p>
            </div>

            {{-- List Jadwal --}}
            <div class="divide-y divide-slate-100">
                <template x-for="(jadwal, index) in jadwalList" :key="jadwal.id">
                    <div
                        :class="selectedJadwal && selectedJadwal.id === jadwal.id ? 'bg-blue-50 border-l-4 border-blue-500' : 'hover:bg-slate-50 border-l-4 border-transparent'"
                        class="p-4 transition-colors"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            {{-- Info utama --}}
                            <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Berangkat</p>
                                    <p class="text-lg font-bold text-slate-800" x-text="jadwal.waktu_berangkat ? jadwal.waktu_berangkat.substring(0,5) : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Bus</p>
                                    <p class="text-sm font-semibold text-slate-700" x-text="jadwal.bus ? jadwal.bus.tipe_bus : '-'"></p>
                                    <p class="text-xs text-slate-400" x-text="jadwal.bus ? jadwal.bus.nomor_polisi : ''"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Pool</p>
                                    <p class="text-sm text-slate-700" x-text="jadwal.pool ? jadwal.pool.nama_pool : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 mb-0.5">Sisa Kursi</p>
                                    <p class="text-sm font-semibold" :class="jadwal.kursi_tersedia > 5 ? 'text-green-600' : jadwal.kursi_tersedia > 0 ? 'text-amber-600' : 'text-red-600'"
                                       x-text="jadwal.kursi_tersedia + ' kursi'"></p>
                                </div>
                            </div>

                            {{-- Harga & Tombol --}}
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-1 sm:min-w-[140px]">
                                <p class="text-base font-bold text-blue-700" x-text="'Rp ' + formatRupiah(jadwal.harga_tiket)"></p>
                                <p class="text-xs text-slate-400">/kursi</p>
                                <button
                                    @click="pilihJadwal(jadwal)"
                                    :disabled="jadwal.kursi_tersedia === 0"
                                    :class="selectedJadwal && selectedJadwal.id === jadwal.id
                                        ? 'bg-blue-600 text-white'
                                        : jadwal.kursi_tersedia === 0
                                            ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                            : 'bg-blue-600 hover:bg-blue-700 text-white'"
                                    class="w-full sm:w-auto px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                                >
                                    <span x-text="selectedJadwal && selectedJadwal.id === jadwal.id ? '✓ Dipilih' : jadwal.kursi_tersedia === 0 ? 'Habis' : 'Pilih'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ===== STEP 2: DENAH KURSI + FORM PENUMPANG ===== --}}
    <div x-show="selectedJadwal" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="font-semibold text-slate-800">Langkah 2 — Pilih Kursi & Data Penumpang</h2>
                <p class="text-xs text-slate-500" x-text="selectedJadwal ? selectedJadwal.rute.asal + ' → ' + selectedJadwal.rute.tujuan + ' · ' + (selectedJadwal.waktu_berangkat ? selectedJadwal.waktu_berangkat.substring(0,5) : '') : ''"></p>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-xs text-slate-500">Harga/kursi</p>
                <p class="font-bold text-blue-700" x-text="selectedJadwal ? 'Rp ' + formatRupiah(selectedJadwal.harga_tiket) : ''"></p>
            </div>
        </div>

        <div class="p-5">
            <div class="flex flex-col lg:flex-row gap-6">
                {{-- ======= KIRI: DENAH KURSI ======= --}}
                <div class="lg:w-80 flex-shrink-0">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                        {{-- Legend --}}
                        <div class="flex items-center gap-4 mb-4 text-xs text-slate-600">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-md bg-white border border-slate-300"></div>
                                <span>Tersedia</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-md bg-slate-200"></div>
                                <span>Terisi</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-md bg-blue-600"></div>
                                <span>Dipilih</span>
                            </div>
                        </div>

                        {{-- Bus body --}}
                        <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
                            {{-- Setir --}}
                            <div class="flex justify-end mb-3 pb-2 border-b border-dashed border-slate-200">
                                <div class="w-10 h-10 rounded-full border-4 border-slate-300 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="3"/>
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Grid Kursi (2+2 layout) --}}
                            <div class="space-y-1.5" x-show="selectedJadwal">
                                <template x-for="row in seatRows" :key="row">
                                    <div class="flex items-center gap-1.5">
                                        {{-- Kursi A --}}
                                        <template x-if="getSeatNum(row, 'A') <= (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <button
                                                type="button"
                                                @click="toggleKursi(getSeatNum(row, 'A'), row + 'A')"
                                                :disabled="isKursiTerisi(getSeatNum(row, 'A'))"
                                                :class="getKursiClass(getSeatNum(row, 'A'))"
                                                class="w-9 h-9 rounded-lg text-xs font-bold transition-all duration-150 border"
                                                :title="'Kursi ' + row + 'A'"
                                                x-text="row + 'A'"
                                            ></button>
                                        </template>
                                        <template x-if="getSeatNum(row, 'A') > (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <div class="w-9 h-9"></div>
                                        </template>

                                        {{-- Kursi B --}}
                                        <template x-if="getSeatNum(row, 'B') <= (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <button
                                                type="button"
                                                @click="toggleKursi(getSeatNum(row, 'B'), row + 'B')"
                                                :disabled="isKursiTerisi(getSeatNum(row, 'B'))"
                                                :class="getKursiClass(getSeatNum(row, 'B'))"
                                                class="w-9 h-9 rounded-lg text-xs font-bold transition-all duration-150 border"
                                                :title="'Kursi ' + row + 'B'"
                                                x-text="row + 'B'"
                                            ></button>
                                        </template>
                                        <template x-if="getSeatNum(row, 'B') > (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <div class="w-9 h-9"></div>
                                        </template>

                                        {{-- Lorong --}}
                                        <div class="flex-1"></div>

                                        {{-- Kursi C --}}
                                        <template x-if="getSeatNum(row, 'C') <= (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <button
                                                type="button"
                                                @click="toggleKursi(getSeatNum(row, 'C'), row + 'C')"
                                                :disabled="isKursiTerisi(getSeatNum(row, 'C'))"
                                                :class="getKursiClass(getSeatNum(row, 'C'))"
                                                class="w-9 h-9 rounded-lg text-xs font-bold transition-all duration-150 border"
                                                :title="'Kursi ' + row + 'C'"
                                                x-text="row + 'C'"
                                            ></button>
                                        </template>
                                        <template x-if="getSeatNum(row, 'C') > (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <div class="w-9 h-9"></div>
                                        </template>

                                        {{-- Kursi D --}}
                                        <template x-if="getSeatNum(row, 'D') <= (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <button
                                                type="button"
                                                @click="toggleKursi(getSeatNum(row, 'D'), row + 'D')"
                                                :disabled="isKursiTerisi(getSeatNum(row, 'D'))"
                                                :class="getKursiClass(getSeatNum(row, 'D'))"
                                                class="w-9 h-9 rounded-lg text-xs font-bold transition-all duration-150 border"
                                                :title="'Kursi ' + row + 'D'"
                                                x-text="row + 'D'"
                                            ></button>
                                        </template>
                                        <template x-if="getSeatNum(row, 'D') > (selectedJadwal ? selectedJadwal.bus.jumlah_kursi : 0)">
                                            <div class="w-9 h-9"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-3 pt-2 border-t border-dashed border-slate-200 text-center text-xs text-slate-400 font-medium tracking-widest">
                                — PINTU KELUAR —
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-slate-500 text-center">
                            Klik kursi untuk memilih/membatalkan
                        </div>
                    </div>
                </div>

                {{-- ======= KANAN: FORM PENUMPANG ======= --}}
                <div class="flex-1">
                    <div x-show="kursiDipilih.length === 0" class="h-full flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-slate-600">Belum ada kursi dipilih</p>
                        <p class="text-sm text-slate-400 mt-1">Klik kursi di denah untuk memilih tempat duduk</p>
                    </div>

                    <div x-show="kursiDipilih.length > 0" x-cloak>
                        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <span>Data Penumpang</span>
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full" x-text="kursiDipilih.length + ' kursi'"></span>
                        </h3>

                        <div class="space-y-3">
                            <template x-for="(item, index) in kursiDipilih" :key="item.nomor">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 group">
                                    <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" x-text="item.label"></div>
                                    <div class="flex-1">
                                        <label class="text-xs font-medium text-slate-500 mb-1 block" x-text="'Nama Penumpang Kursi ' + item.label"></label>
                                        <input
                                            type="text"
                                            x-model="item.nama"
                                            :placeholder="'Nama penumpang kursi ' + item.label"
                                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                    </div>
                                    <button
                                        type="button"
                                        @click="hapusKursi(item.nomor)"
                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0"
                                        title="Hapus kursi"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======= STEP 3: PULANG-PERGI ======= --}}
            <div class="mt-6 pt-5 border-t border-slate-200">
                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <input
                        type="checkbox"
                        x-model="isRoundTrip"
                        @change="handleRoundTripChange()"
                        class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                    >
                    <div>
                        <span class="font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">Tiket Pulang-Pergi</span>
                        <p class="text-xs text-slate-500">Pilih jadwal untuk rute kembali</p>
                    </div>
                </label>

                {{-- Jadwal Pulang --}}
                <div x-show="isRoundTrip" x-cloak class="mt-4">
                    <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            Pilih Jadwal Pulang
                            <span class="text-blue-600 font-normal text-xs" x-show="selectedJadwal" x-text="selectedJadwal ? '(' + selectedJadwal.rute.tujuan + ' → ' + selectedJadwal.rute.asal + ')' : ''"></span>
                        </h4>

                        <div x-show="loadingPulang" class="flex items-center gap-2 text-sm text-blue-600 py-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Memuat jadwal pulang...
                        </div>

                        <div x-show="!loadingPulang && jadwalPulangList.length === 0" class="text-sm text-blue-600 py-2 text-center">
                            Tidak ada jadwal pulang tersedia
                        </div>

                        <div x-show="!loadingPulang && jadwalPulangList.length > 0" class="space-y-2">
                            <template x-for="jp in jadwalPulangList" :key="jp.id">
                                <label
                                    :class="selectedJadwalPulang && selectedJadwalPulang.id === jp.id ? 'border-blue-500 bg-white shadow-sm' : 'border-blue-200 bg-white/70 hover:bg-white'"
                                    class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                >
                                    <input
                                        type="radio"
                                        :value="jp.id"
                                        x-model="selectedJadwalPulangId"
                                        @change="pilihJadwalPulang(jp)"
                                        class="text-blue-600 focus:ring-blue-500"
                                        name="jadwal_pulang_radio"
                                    >
                                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
                                        <div>
                                            <p class="text-xs text-slate-500">Tanggal</p>
                                            <p class="font-semibold text-slate-800" x-text="jp.tanggal_berangkat"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Berangkat</p>
                                            <p class="font-semibold text-slate-800" x-text="jp.waktu_berangkat ? jp.waktu_berangkat.substring(0,5) : '-'"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Bus</p>
                                            <p class="font-medium text-slate-700" x-text="jp.tipe_bus"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Harga</p>
                                            <p class="font-bold text-blue-700" x-text="'Rp ' + formatRupiah(jp.harga_tiket)"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs" :class="jp.kursi_tersedia > 0 ? 'text-green-600' : 'text-red-500'" x-text="jp.kursi_tersedia + ' kursi'"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== STEP 4: DATA PEMESAN + PEMBAYARAN ===== --}}
    <div x-show="kursiDipilih.length > 0" x-cloak>
        <form
            method="POST"
            action="{{ route('sales.pemesanan.store') }}"
            @submit="validateAndSubmit($event)"
            id="form-pemesanan"
        >
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="jadwal_id" :value="selectedJadwal ? selectedJadwal.id : ''">
            <input type="hidden" name="is_round_trip" :value="isRoundTrip ? '1' : '0'">
            <input type="hidden" name="jadwal_pulang_id" :value="selectedJadwalPulang ? selectedJadwalPulang.id : ''">

            {{-- Kursi pergi (array) --}}
            <template x-for="item in kursiDipilih" :key="'kursi-' + item.nomor">
                <input type="hidden" name="kursi[]" :value="item.nomor">
            </template>
            <template x-for="item in kursiDipilih" :key="'nama-' + item.nomor">
                <input type="hidden" name="nama_penumpang[]" :value="item.nama">
            </template>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Langkah 3 — Data Pemesan & Pembayaran</h2>
                        <p class="text-xs text-slate-500">Isi data pemesan dan metode pembayaran</p>
                    </div>
                </div>

                <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Kolom Kiri: Data Pemesan + Metode Bayar --}}
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide block mb-1.5">Nama Pemesan <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="nama_pemesan"
                                x-model="form.nama_pemesan"
                                placeholder="Nama lengkap pemesan"
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide block mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="no_hp_pemesan"
                                x-model="form.no_hp"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide block mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="metode in ['Cash', 'Transfer', 'E-Wallet']" :key="metode">
                                    <label
                                        :class="form.metode_pembayaran === metode ? 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500' : 'border-slate-200 hover:border-slate-300 text-slate-600'"
                                        class="flex flex-col items-center gap-1.5 p-3 rounded-xl border cursor-pointer transition-all text-center"
                                    >
                                        <input type="radio" name="metode_pembayaran" :value="metode" x-model="form.metode_pembayaran" class="sr-only" required>
                                        <svg x-show="metode === 'Cash'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <svg x-show="metode === 'Transfer'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <svg x-show="metode === 'E-Wallet'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs font-semibold" x-text="metode"></span>
                                    </label>
                                </template>
                            </div>
                            <p x-show="form.metode_pembayaran === 'Cash'" class="mt-2 text-xs text-green-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></path></svg>
                                Status langsung: Lunas
                            </p>
                            <p x-show="form.metode_pembayaran !== 'Cash' && form.metode_pembayaran !== ''" class="mt-2 text-xs text-amber-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></path></svg>
                                Status: Pending — konfirmasi manual setelah transfer
                            </p>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Ringkasan Harga --}}
                    <div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-3">
                            <h3 class="font-semibold text-slate-700 text-sm">Ringkasan Pemesanan</h3>

                            {{-- Tiket Pergi --}}
                            <div class="space-y-1.5">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tiket Pergi</p>
                                <template x-for="item in kursiDipilih" :key="'sum-' + item.nomor">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-600" x-text="'Kursi ' + item.label + (item.nama ? ' — ' + item.nama : '')"></span>
                                        <span class="font-medium text-slate-800" x-text="'Rp ' + formatRupiah(selectedJadwal ? selectedJadwal.harga_tiket : 0)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between text-sm font-semibold border-t border-slate-200 pt-1.5">
                                    <span class="text-slate-700" x-text="'Subtotal (' + kursiDipilih.length + ' kursi)'"></span>
                                    <span class="text-blue-700" x-text="'Rp ' + formatRupiah(subtotalPergi)"></span>
                                </div>
                            </div>

                            {{-- Tiket Pulang --}}
                            <div x-show="isRoundTrip && selectedJadwalPulang" class="space-y-1.5 border-t border-slate-200 pt-3">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tiket Pulang</p>
                                <template x-for="item in kursiDipilih" :key="'sum-pulang-' + item.nomor">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-600" x-text="'Kursi ' + item.label"></span>
                                        <span class="font-medium text-slate-800" x-text="'Rp ' + formatRupiah(selectedJadwalPulang ? selectedJadwalPulang.harga_tiket : 0)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between text-sm font-semibold border-t border-slate-200 pt-1.5">
                                    <span class="text-slate-700" x-text="'Subtotal (' + kursiDipilih.length + ' kursi)'"></span>
                                    <span class="text-blue-700" x-text="'Rp ' + formatRupiah(subtotalPulang)"></span>
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="border-t-2 border-slate-300 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800">TOTAL BAYAR</span>
                                    <span class="text-xl font-extrabold text-blue-700" x-text="'Rp ' + formatRupiah(totalBayar)"></span>
                                </div>
                                <p x-show="isRoundTrip && selectedJadwalPulang" class="text-xs text-slate-500 mt-1 text-right">Termasuk PP (Pulang-Pergi)</p>
                            </div>
                        </div>

                        {{-- Alert jika round trip tapi belum pilih jadwal pulang --}}
                        <div x-show="isRoundTrip && !selectedJadwalPulang" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 flex items-start gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Anda memilih pulang-pergi namun belum memilih jadwal pulang. Pilih jadwal pulang di atas atau nonaktifkan opsi ini.</span>
                        </div>

                        {{-- Tombol Submit --}}
                        <button
                            type="submit"
                            :disabled="!canSubmit || submitting"
                            class="w-full mt-4 py-3.5 px-6 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                            :class="canSubmit && !submitting
                                ? 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer hover:shadow-md'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                        >
                            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="submitting ? 'Memproses...' : 'Proses Pembayaran'"></span>
                        </button>

                        <p class="text-center text-xs text-slate-400 mt-2" x-show="!canSubmit && !submitting">
                            Lengkapi data di atas untuk melanjutkan
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function pemesananWizard() {
    return {
        step: 1,
        today: new Date().toISOString().split('T')[0],

        // Search state
        search: { asal: '', tujuan: '', tanggal: '', tipe_bus: '' },
        loadingSearch: false,
        searchDone: false,
        jadwalList: [],

        // Step 2 - jadwal pergi
        selectedJadwal: null,
        kursiTerisiPergi: [],
        seatRows: [],
        kursiDipilih: [],

        // Round trip
        isRoundTrip: false,
        loadingPulang: false,
        jadwalPulangList: [],
        selectedJadwalPulang: null,
        selectedJadwalPulangId: null,

        // Form
        form: { nama_pemesan: '', no_hp: '', metode_pembayaran: '' },
        submitting: false,

        init() {
            this.seatRows = Array.from({ length: 15 }, (_, i) => i + 1);
        },

        // ===== PENCARIAN JADWAL =====
        async cariJadwal() {
            if (!this.search.asal || !this.search.tujuan || !this.search.tanggal) return;

            this.loadingSearch = true;
            this.searchDone = false;
            this.jadwalList = [];
            this.selectedJadwal = null;
            this.kursiDipilih = [];
            this.isRoundTrip = false;
            this.selectedJadwalPulang = null;

            try {
                const params = new URLSearchParams({
                    asal: this.search.asal,
                    tujuan: this.search.tujuan,
                    tanggal_berangkat: this.search.tanggal,
                    ...(this.search.tipe_bus ? { tipe_bus: this.search.tipe_bus } : {})
                });

                const res = await fetch(`{{ route('sales.pemesanan.cari') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    method: 'POST',
                    body: new URLSearchParams({
                        asal: this.search.asal,
                        tujuan: this.search.tujuan,
                        tanggal_berangkat: this.search.tanggal,
                        ...(this.search.tipe_bus ? { tipe_bus: this.search.tipe_bus } : {}),
                        _token: '{{ csrf_token() }}'
                    })
                });

                const json = await res.json();
                if (json.success) {
                    this.jadwalList = json.data;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingSearch = false;
                this.searchDone = true;
                this.step = 1;
            }
        },

        // ===== PILIH JADWAL PERGI =====
        async pilihJadwal(jadwal) {
            if (jadwal.kursi_tersedia === 0) return;

            this.selectedJadwal = jadwal;
            this.kursiDipilih = [];
            this.isRoundTrip = false;
            this.selectedJadwalPulang = null;
            this.selectedJadwalPulangId = null;
            this.jadwalPulangList = [];
            this.step = 2;

            // Load kursi terisi via API
            try {
                const res = await fetch(`{{ url('sales/pemesanan/kursi-terisi') }}/${jadwal.id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success) {
                    this.kursiTerisiPergi = json.kursi_terisi;
                    // Recalculate row count
                    const totalKursi = json.jumlah_kursi;
                    const rowCount = Math.ceil(totalKursi / 4);
                    this.seatRows = Array.from({ length: rowCount }, (_, i) => i + 1);
                }
            } catch (e) {
                this.kursiTerisiPergi = jadwal.kursi_terisi || [];
            }
        },

        // ===== DENAH KURSI =====
        getSeatNum(row, col) {
            const colIndex = { A: 0, B: 1, C: 2, D: 3 };
            return (row - 1) * 4 + colIndex[col] + 1;
        },

        isKursiTerisi(nomor) {
            return this.kursiTerisiPergi.includes(nomor);
        },

        isCursiDipilih(nomor) {
            return this.kursiDipilih.some(k => k.nomor === nomor);
        },

        getKursiClass(nomor) {
            if (this.isKursiTerisi(nomor)) {
                return 'bg-slate-200 text-slate-400 cursor-not-allowed border-slate-200';
            }
            if (this.isCursiDipilih(nomor)) {
                return 'bg-blue-600 text-white border-blue-600 shadow-sm scale-95';
            }
            return 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700';
        },

        toggleKursi(nomor, label) {
            if (this.isKursiTerisi(nomor)) return;

            const idx = this.kursiDipilih.findIndex(k => k.nomor === nomor);
            if (idx >= 0) {
                this.kursiDipilih.splice(idx, 1);
            } else {
                this.kursiDipilih.push({ nomor, label, nama: '' });
            }
        },

        hapusKursi(nomor) {
            const idx = this.kursiDipilih.findIndex(k => k.nomor === nomor);
            if (idx >= 0) this.kursiDipilih.splice(idx, 1);
        },

        // ===== ROUND TRIP =====
        async handleRoundTripChange() {
            if (!this.isRoundTrip || !this.selectedJadwal) return;

            this.loadingPulang = true;
            this.jadwalPulangList = [];
            this.selectedJadwalPulang = null;
            this.selectedJadwalPulangId = null;

            try {
                const res = await fetch('{{ route("sales.pemesanan.jadwalPulang") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ jadwal_pergi_id: this.selectedJadwal.id })
                });
                const json = await res.json();
                if (json.success) {
                    this.jadwalPulangList = json.data;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingPulang = false;
            }
        },

        pilihJadwalPulang(jadwal) {
            this.selectedJadwalPulang = jadwal;
        },

        // ===== HARGA & VALIDASI =====
        get subtotalPergi() {
            if (!this.selectedJadwal) return 0;
            return parseFloat(this.selectedJadwal.harga_tiket) * this.kursiDipilih.length;
        },

        get subtotalPulang() {
            if (!this.selectedJadwalPulang || !this.isRoundTrip) return 0;
            return parseFloat(this.selectedJadwalPulang.harga_tiket) * this.kursiDipilih.length;
        },

        get totalBayar() {
            return this.subtotalPergi + this.subtotalPulang;
        },

        get canSubmit() {
            if (!this.selectedJadwal) return false;
            if (this.kursiDipilih.length === 0) return false;
            if (this.kursiDipilih.some(k => !k.nama.trim())) return false;
            if (!this.form.nama_pemesan.trim()) return false;
            if (!this.form.no_hp.trim()) return false;
            if (!this.form.metode_pembayaran) return false;
            if (this.isRoundTrip && !this.selectedJadwalPulang) return false;
            return true;
        },

        validateAndSubmit(e) {
            if (!this.canSubmit) {
                e.preventDefault();
                return;
            }
            this.submitting = true;
        },

        // ===== HELPERS =====
        formatRupiah(val) {
            if (!val) return '0';
            return parseInt(val).toLocaleString('id-ID');
        },

        formatTanggal(tgl) {
            if (!tgl) return '';
            const d = new Date(tgl);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    };
}
</script>
@endsection
