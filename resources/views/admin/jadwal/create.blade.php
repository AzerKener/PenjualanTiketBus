@extends('layouts.admin')

@section('page-title', 'Tambah Jadwal')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.jadwal.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Jadwal</h1>
            <p class="text-sm text-slate-500 mt-0.5">Buat jadwal keberangkatan baru</p>
        </div>
    </div>

    {{-- Conflict Alert --}}
    @if(session('conflict'))
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ session('conflict') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm font-medium text-red-700 mb-2">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-sm text-red-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.jadwal.store') }}" method="POST">
            @csrf
                <input type="hidden" name="status" value="menunggu">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- Bus --}}
                <div>
                    <label for="bus_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Bus <span class="text-red-500">*</span>
                    </label>
                    <select id="bus_id"
                            name="bus_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('bus_id') border-red-400 @enderror">
                        <option value="" disabled selected>-- Pilih Bus --</option>
                        @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                            {{ $bus->nomor_polisi }} — {{ $bus->tipe_bus }} ({{ $bus->jumlah_kursi }} kursi)
                        </option>
                        @endforeach
                    </select>
                    @error('bus_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rute --}}
                <div>
                    <label for="rute_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Rute <span class="text-red-500">*</span>
                    </label>
                    <select id="rute_id"
                            name="rute_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('rute_id') border-red-400 @enderror">
                        <option value="" disabled selected>-- Pilih Rute --</option>
                        @foreach($rutes as $rute)
                        <option value="{{ $rute->id }}" data-asal="{{ $rute->asal }}" data-tujuan="{{ $rute->tujuan }}" {{ old('rute_id') == $rute->id ? 'selected' : '' }}>
                            {{ $rute->asal }} → {{ $rute->tujuan }}
                        </option>
                        @endforeach
                    </select>
                    @error('rute_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pool Asal --}}
                <div>
                    <label for="pool_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Pool Asal <span class="text-red-500">*</span>
                    </label>
                    <select id="pool_id"
                            name="pool_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('pool_id') border-red-400 @enderror">
                        <option value="" disabled selected>-- Pilih Pool Asal --</option>
                        @foreach($pools as $pool)
                        <option value="{{ $pool->id }}" data-lokasi="{{ $pool->lokasi }}" {{ old('pool_id') == $pool->id ? 'selected' : '' }}>
                            {{ $pool->nama_pool }}
                        </option>
                        @endforeach
                    </select>
                    @error('pool_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pool Tujuan --}}
                <div>
                    <label for="pool_tujuan_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Pool Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select id="pool_tujuan_id"
                            name="pool_tujuan_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('pool_tujuan_id') border-red-400 @enderror">
                        <option value="" disabled selected>-- Pilih Pool Tujuan --</option>
                        @foreach($pools as $pool)
                        <option value="{{ $pool->id }}" data-lokasi="{{ $pool->lokasi }}" {{ old('pool_tujuan_id') == $pool->id ? 'selected' : '' }}>
                            {{ $pool->nama_pool }}
                        </option>
                        @endforeach
                    </select>
                    @error('pool_tujuan_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Berangkat --}}
                <div>
                    <label for="tanggal_berangkat" class="block text-sm font-medium text-slate-700 mb-1">
                        Tanggal Berangkat <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="tanggal_berangkat"
                           name="tanggal_berangkat"
                           value="{{ old('tanggal_berangkat') }}"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('tanggal_berangkat') border-red-400 @enderror">
                    @error('tanggal_berangkat')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waktu Berangkat --}}
                <div>
                    <label for="waktu_berangkat" class="block text-sm font-medium text-slate-700 mb-1">
                        Waktu Berangkat <span class="text-red-500">*</span>
                    </label>
                    <input type="time"
                           id="waktu_berangkat"
                           name="waktu_berangkat"
                           value="{{ old('waktu_berangkat') }}"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('waktu_berangkat') border-red-400 @enderror">
                    @error('waktu_berangkat')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estimasi Tiba --}}
                <div>
                    <label for="estimasi_tiba" class="block text-sm font-medium text-slate-700 mb-1">
                        Estimasi Tiba <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="estimasi_tiba" 
                           name="estimasi_tiba" 
                           value="{{ old('estimasi_tiba') }}" 
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                        @error('estimasi_tiba') border-red-400 @enderror">
                    @error('estimasi_tiba')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Tiket --}}
                <div>
                    <label for="harga_tiket" class="block text-sm font-medium text-slate-700 mb-1">
                        Harga Tiket (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="harga_tiket"
                           name="harga_tiket"
                           value="{{ old('harga_tiket') }}"
                           min="0"
                           step="1000"
                           placeholder="Contoh: 150000"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('harga_tiket') border-red-400 @enderror">
                    @error('harga_tiket')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Supir 1 --}}
                <div>
                    <label for="supir1_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Supir 1 <span class="text-red-500">*</span>
                    </label>
                    <select id="supir1_id"
                            name="supir1_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('supir1_id') border-red-400 @enderror">
                        <option value="" disabled selected>-- Pilih Supir --</option>
                        @foreach($supirs as $supir)
                        <option value="{{ $supir->id }}" data-pool="{{ $supir->pool_id }}" {{ old('supir1_id') == $supir->id ? 'selected' : '' }}>
                            {{ $supir->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('supir1_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Supir 2 --}}
                <div>
                    <label for="supir2_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Supir 2 <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <select id="supir2_id"
                            name="supir2_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('supir2_id') border-red-400 @enderror">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($supirs as $supir)
                        <option value="{{ $supir->id }}" data-pool="{{ $supir->pool_id }}" {{ old('supir2_id') == $supir->id ? 'selected' : '' }}>
                            {{ $supir->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('supir2_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kenek --}}
                <div>
                    <label for="kenek_id" class="block text-sm font-medium text-slate-700 mb-1">
                        Kenek <span class="text-slate-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <select id="kenek_id"
                            name="kenek_id"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition @error('kenek_id') border-red-400 @enderror">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($keneks as $kenek)
                        <option value="{{ $kenek->id }}" data-pool="{{ $kenek->pool_id }}" {{ old('kenek_id') == $kenek->id ? 'selected' : '' }}>
                            {{ $kenek->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('kenek_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-6 border-t border-slate-100 mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
                    Simpan Jadwal
                </button>
                <a href="{{ route('admin.jadwal.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ruteSelect = document.getElementById('rute_id');
        const poolAsalSelect = document.getElementById('pool_id');
        const poolTujuanSelect = document.getElementById('pool_tujuan_id');

        const supir1Select = document.getElementById('supir1_id');
        const supir2Select = document.getElementById('supir2_id');
        const kenekSelect = document.getElementById('kenek_id');

        // Simpan semua opsi ke array
        const allPoolAsalOptions = Array.from(poolAsalSelect.options).filter(opt => opt.value !== "");
        const allPoolTujuanOptions = Array.from(poolTujuanSelect.options).filter(opt => opt.value !== "");
        const allSupir1Options = Array.from(supir1Select.options).filter(opt => opt.value !== "");
        const allSupir2Options = Array.from(supir2Select.options).filter(opt => opt.value !== "");
        const allKenekOptions = Array.from(kenekSelect.options).filter(opt => opt.value !== "");

        function updatePools() {
            const selectedRute = ruteSelect.options[ruteSelect.selectedIndex];
            
            if (!selectedRute.value) return;

            const asal = selectedRute.getAttribute('data-asal').toLowerCase();
            const tujuan = selectedRute.getAttribute('data-tujuan').toLowerCase();

            // Filter Pool Asal
            let firstAsalFound = false;
            allPoolAsalOptions.forEach(opt => {
                const lokasi = opt.getAttribute('data-lokasi').toLowerCase();
                if (lokasi.includes(asal)) {
                    opt.style.display = '';
                    if (!firstAsalFound && poolAsalSelect.value === "") {
                        firstAsalFound = true;
                    }
                } else {
                    opt.style.display = 'none';
                    if (poolAsalSelect.value === opt.value) {
                        poolAsalSelect.value = "";
                    }
                }
            });

            // Filter Pool Tujuan
            let firstTujuanFound = false;
            allPoolTujuanOptions.forEach(opt => {
                const lokasi = opt.getAttribute('data-lokasi').toLowerCase();
                if (lokasi.includes(tujuan)) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (poolTujuanSelect.value === opt.value) {
                        poolTujuanSelect.value = "";
                    }
                }
            });
            
            updatePetugas(); // Panggil ini juga agar petugas terfilter saat rute (dan pool) berubah
        }

        function updatePetugas() {
            const selectedPoolId = poolAsalSelect.value;
            if (!selectedPoolId) return;

            // Supir 1
            allSupir1Options.forEach(opt => {
                if (opt.getAttribute('data-pool') === selectedPoolId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (supir1Select.value === opt.value) supir1Select.value = "";
                }
            });

            // Supir 2
            allSupir2Options.forEach(opt => {
                if (opt.getAttribute('data-pool') === selectedPoolId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (supir2Select.value === opt.value) supir2Select.value = "";
                }
            });

            // Kenek
            allKenekOptions.forEach(opt => {
                if (opt.getAttribute('data-pool') === selectedPoolId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (kenekSelect.value === opt.value) kenekSelect.value = "";
                }
            });
        }

        // Jalankan saat berubah
        ruteSelect.addEventListener('change', updatePools);
        poolAsalSelect.addEventListener('change', updatePetugas);

        // Jalankan saat pertama kali dimuat
        if (ruteSelect.value) {
            updatePools();
        }
        if (poolAsalSelect.value) {
            updatePetugas();
        }
    });
</script>
@endsection
