@extends('layouts.sales')
@section('title', 'Pilih Kursi — ' . $jadwal->rute->asal . ' → ' . $jadwal->rute->tujuan)

@section('content')
<div class="max-w-5xl mx-auto"
    x-data='pemesananSales(@json($semuaKursi), @json($kursiTerisi), {{ $jadwal->id }}, {{ $jadwal->harga_tiket }})'>

    {{-- Back + Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('sales.pemesanan.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">{{ $jadwal->rute->asal }} → {{ $jadwal->rute->tujuan }}</h1>
            <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('dddd, D MMMM Y') }} • {{ substr($jadwal->waktu_berangkat, 0, 5) }} WIB</p>
        </div>
    </div>

    {{-- Info strip --}}
    <div class="bg-amber-600 text-white rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex flex-wrap gap-4 text-sm">
            <span>🚌 {{ $jadwal->bus->nomor_polisi }} ({{ $jadwal->bus->tipe_bus }})</span>
            <span>📍 {{ $jadwal->pool->nama_pool }}</span>
            <span>💺 {{ $jadwal->bus->jumlah_kursi }} kursi</span>
        </div>
        <div class="text-right">
            <p class="text-amber-200 text-xs">Harga per kursi</p>
            <p class="text-2xl font-extrabold">Rp {{ number_format($jadwal->harga_tiket, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kiri: Denah Kursi --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-slate-800 mb-4">Pilih Kursi</h2>
            <div class="flex items-center gap-4 mb-4 text-xs text-slate-500">
                <div class="flex items-center gap-1.5"><div class="w-6 h-6 rounded-lg border-2 border-slate-300 bg-white"></div><span>Tersedia</span></div>
                <div class="flex items-center gap-1.5"><div class="w-6 h-6 rounded-lg bg-amber-500"></div><span>Dipilih</span></div>
                <div class="flex items-center gap-1.5"><div class="w-6 h-6 rounded-lg bg-slate-200"></div><span>Terisi</span></div>
            </div>

            <div class="overflow-y-auto max-h-80">
                <div class="grid gap-2" :style="'grid-template-columns: 1fr 1fr 24px 1fr 1fr'">
                    <template x-for="(kursi, idx) in semuaKursi" :key="kursi">
                        <div class="contents">
                            <template x-if="idx % 4 === 2"><div></div></template>
                            <button type="button"
                                :class="{
                                    'bg-amber-500 text-white border-amber-500 shadow-md': dipilih.includes(kursi),
                                    'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200': terisi.includes(kursi),
                                    'bg-white border-slate-300 hover:border-amber-400 hover:bg-amber-50 text-slate-700 cursor-pointer': !dipilih.includes(kursi) && !terisi.includes(kursi)
                                }"
                                class="h-10 rounded-lg border-2 text-xs font-bold transition-all"
                                :disabled="terisi.includes(kursi)"
                                @click="toggleKursi(kursi)"
                                x-text="kursi">
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="dipilih.length > 0" x-cloak class="mt-4 p-3 bg-amber-50 rounded-xl border border-amber-100">
                <p class="text-xs text-amber-700 font-semibold mb-1">Kursi dipilih (<span x-text="dipilih.length"></span>):</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="k in dipilih" :key="'tag-'+k">
                        <span class="px-2 py-0.5 bg-amber-500 text-white rounded text-xs font-mono font-bold" x-text="k"></span>
                    </template>
                </div>
            </div>

            {{-- Tiket Pulang-Pergi --}}
            <div class="mt-5 border-t border-slate-100 pt-5">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input type="checkbox" x-model="isRoundTrip" class="w-5 h-5 rounded text-amber-500 border-slate-300 mt-0.5 flex-shrink-0">
                    <div>
                        <p class="font-semibold text-slate-700 group-hover:text-amber-600 transition-colors text-sm">Tiket Pulang-Pergi</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tambah perjalanan kembali</p>
                    </div>
                </label>

                <div x-show="isRoundTrip" x-cloak class="mt-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Jadwal Pulang</label>
                    <select x-model="jadwalPulangId" @change="loadKursiPulang($event.target.value)"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @if($jadwalPulangList->isEmpty())
                            <option value="" disabled selected>-- Tidak ada jadwal pulang tersedia --</option>
                        @else
                            <option value="">-- Pilih Jadwal Pulang --</option>
                            @foreach($jadwalPulangList as $jp)
                            <option value="{{ $jp->id }}" data-harga="{{ $jp->harga_tiket }}">
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
            </div>
        </div>

        {{-- Kanan: Form Data --}}
        <div class="space-y-5">

            {{-- Data Pemesan --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-slate-800 mb-4">Data Pemesan (Pelanggan)</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap Pemesan</label>
                        <input type="text" x-model="namaPemesan" placeholder="Nama pelanggan"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">No. HP Pemesan</label>
                        <input type="text" x-model="noHpPemesan" placeholder="Contoh: 08123456789"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            {{-- Data Penumpang --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-slate-800 mb-4">Nama Penumpang</h2>
                <div x-show="dipilih.length === 0" class="text-sm text-slate-400 text-center py-6">
                    Pilih kursi terlebih dahulu
                </div>
                <div class="space-y-3">
                    <template x-for="(kursi, i) in dipilih" :key="'form-'+kursi">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center text-xs font-bold flex-shrink-0" x-text="kursi"></div>
                            <input type="text" x-model="namaPenumpang[i]" placeholder="Nama penumpang"
                                   class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Ringkasan & Submit --}}
            <div class="bg-slate-800 text-white rounded-2xl p-6">
                <h3 class="font-bold mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between text-slate-300">
                        <span>Tiket pergi</span>
                        <span x-text="dipilih.length + ' × Rp ' + hargaPergi.toLocaleString('id-ID')"></span>
                    </div>
                    <div x-show="isRoundTrip && hargaPulang > 0" x-cloak class="flex justify-between text-slate-300">
                        <span>Tiket pulang</span>
                        <span x-text="dipilih.length + ' × Rp ' + hargaPulang.toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="border-t border-slate-700 pt-3 mb-5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300 text-sm">Grand Total</span>
                        <span class="text-2xl font-extrabold text-amber-400" x-text="'Rp ' + totalHarga().toLocaleString('id-ID')"></span>
                    </div>
                    <p class="text-xs text-green-400 mt-1">✓ Pembayaran: Cash (Langsung Lunas)</p>
                </div>

                <form method="POST" action="{{ route('sales.pemesanan.store') }}" x-ref="form" id="formSales">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <input type="hidden" name="is_round_trip" :value="isRoundTrip ? 1 : 0">
                    <input type="hidden" name="jadwal_pulang_id" :value="jadwalPulangId">
                    <input type="hidden" name="nama_pemesan" :value="namaPemesan">
                    <input type="hidden" name="no_hp_pemesan" :value="noHpPemesan">
                    <template x-for="(kursi, i) in dipilih" :key="'hk-'+i">
                        <input type="hidden" :name="'kursi[' + i + ']'" :value="kursi">
                    </template>
                    <template x-for="(nama, i) in namaPenumpang" :key="'hn-'+i">
                        <input type="hidden" :name="'nama_penumpang[' + i + ']'" :value="nama">
                    </template>

                    <button type="button" @click="submit()"
                        :disabled="!bisaSubmit()"
                        :class="bisaSubmit() ? 'bg-amber-500 hover:bg-amber-400 cursor-pointer' : 'bg-slate-600 cursor-not-allowed'"
                        class="w-full text-white font-bold py-3.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="bisaSubmit() ? 'Konfirmasi Pemesanan' : 'Isi semua data dulu'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function pemesananSales(semuaKursi, kursiTerisi, jadwalId, hargaPergi) {
    return {
        semuaKursi,
        terisi: kursiTerisi,
        dipilih: [],
        namaPenumpang: [],
        namaPemesan: '',
        noHpPemesan: '',
        isRoundTrip: false,
        jadwalPulangId: '',
        hargaPergi,
        hargaPulang: 0,

        toggleKursi(kursi) {
            if (this.terisi.includes(kursi)) return;
            const idx = this.dipilih.indexOf(kursi);
            if (idx !== -1) {
                this.dipilih.splice(idx, 1);
                this.namaPenumpang.splice(idx, 1);
            } else {
                this.dipilih.push(kursi);
                this.namaPenumpang.push('');
            }
        },

        async loadKursiPulang(jadwalPId) {
            this.hargaPulang = 0;
            if (!jadwalPId) return;
            const sel = document.querySelector('[x-model="jadwalPulangId"]');
            if (sel) {
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.dataset.harga) this.hargaPulang = parseInt(opt.dataset.harga);
            }
        },

        bisaSubmit() {
            if (this.dipilih.length === 0) return false;
            if (!this.namaPemesan.trim()) return false;
            if (!this.noHpPemesan.trim()) return false;
            for (let i = 0; i < this.namaPenumpang.length; i++) {
                if (!this.namaPenumpang[i] || !this.namaPenumpang[i].trim()) return false;
            }
            if (this.isRoundTrip && !this.jadwalPulangId) return false;
            return true;
        },

        submit() {
            if (!this.bisaSubmit()) return;
            document.getElementById('formSales').submit();
        },

        totalHarga() {
            const pergi = this.dipilih.length * this.hargaPergi;
            const pulang = this.isRoundTrip && this.hargaPulang > 0 ? this.dipilih.length * this.hargaPulang : 0;
            return pergi + pulang;
        }
    }
}
</script>
@endpush
@endsection
