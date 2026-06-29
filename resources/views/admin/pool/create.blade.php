@extends('layouts.admin')

@section('page-title', 'Tambah Pool')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pool.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Pool</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tambahkan pool keberangkatan baru</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-lg">

        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-medium text-red-700 mb-2">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-sm text-red-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.pool.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama Pool --}}
            <div>
                <label for="nama_pool" class="block text-sm font-medium text-slate-700 mb-1">
                    Nama Pool <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nama_pool"
                       name="nama_pool"
                       value="{{ old('nama_pool') }}"
                       placeholder="Contoh: Pool Giwangan"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('nama_pool') border-red-400 @enderror">
                @error('nama_pool')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lokasi --}}
            <div>
                <label for="lokasi" class="block text-sm font-medium text-slate-700 mb-1">
                    Lokasi / Alamat <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="lokasi"
                       name="lokasi"
                       value="{{ old('lokasi') }}"
                       placeholder="Contoh: Jl. Imogiri Timur KM 6, Yogyakarta"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('lokasi') border-red-400 @enderror">
                @error('lokasi')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Latitude --}}
                <div>
                    <label for="latitude" class="block text-sm font-medium text-slate-700 mb-1">Latitude</label>
                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                           placeholder="Contoh: -7.8286"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('latitude') border-red-400 @enderror">
                    @error('latitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Longitude --}}
                <div>
                    <label for="longitude" class="block text-sm font-medium text-slate-700 mb-1">Longitude</label>
                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                           placeholder="Contoh: 110.3958"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('longitude') border-red-400 @enderror">
                    @error('longitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Map Picker --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Lokasi di Peta</label>
                <div id="map" class="w-full h-64 rounded-xl border border-slate-200 z-10"></div>
                <p class="text-xs text-slate-500 mt-1">Klik pada peta untuk otomatis mengisi Latitude dan Longitude.</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
                    Simpan Pool
                </button>
                <a href="{{ route('admin.pool.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Inisialisasi Peta (Default ke pusat Indonesia)
        var map = L.map('map').setView([-2.5489, 118.0149], 5);

        // Tambahkan TileLayer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = null;
        var latInput = document.getElementById('latitude');
        var lngInput = document.getElementById('longitude');
        var lokasiInput = document.getElementById('lokasi');

        // Jika sudah ada isian (misal gagal validasi), taruh marker
        if (latInput.value && lngInput.value) {
            var latLng = [parseFloat(latInput.value), parseFloat(lngInput.value)];
            marker = L.marker(latLng).addTo(map);
            map.setView(latLng, 15);
        }

        // Event saat peta diklik
        map.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(8);
            var lng = e.latlng.lng.toFixed(8);

            // Update Input
            latInput.value = lat;
            lngInput.value = lng;

            // Pindahkan atau buat marker baru
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            // Ambil alamat dari koordinat (Reverse Geocoding)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        lokasiInput.value = data.display_name;
                    }
                })
                .catch(error => console.error('Error fetching address:', error));
        });
    });
</script>
@endsection
