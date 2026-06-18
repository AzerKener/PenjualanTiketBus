@extends('layouts.admin')

@section('page-title', 'Edit Bus')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bus.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Bus</h1>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui data bus <span class="font-semibold text-slate-700">{{ $bus->no_polisi }}</span></p>
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

        <form action="{{ route('admin.bus.update', $bus->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- No. Polisi --}}
            <div>
                <label for="nomor_polisi" class="block text-sm font-medium text-slate-700 mb-1">
                    Nomor Polisi <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nomor_polisi"
                       name="nomor_polisi"
                       value="{{ old('nomor_polisi', $bus->nomor_polisi) }}"
                       placeholder="Contoh: AB 1234 CD"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('nomor_polisi') border-red-400 @enderror">
                @error('nomor_polisi')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Bus --}}
            <div>
                <label for="tipe_bus" class="block text-sm font-medium text-slate-700 mb-1">
                    Tipe Bus <span class="text-red-500">*</span>
                </label>
                <select id="tipe_bus"
                        name="tipe_bus"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white @error('tipe_bus') border-red-400 @enderror">
                    <option value="" disabled>-- Pilih Tipe Bus --</option>
                    <option value="Ekonomi" {{ old('tipe_bus', $bus->tipe_bus) == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="VIP" {{ old('tipe_bus', $bus->tipe_bus) == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="Executive" {{ old('tipe_bus', $bus->tipe_bus) == 'Executive' ? 'selected' : '' }}>Executive</option>
                </select>
                @error('tipe_bus')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Kursi --}}
            <div>
                <label for="jumlah_kursi" class="block text-sm font-medium text-slate-700 mb-1">
                    Jumlah Kursi <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       id="jumlah_kursi"
                       name="jumlah_kursi"
                       value="{{ old('jumlah_kursi', $bus->jumlah_kursi) }}"
                       min="1"
                       max="100"
                       placeholder="Contoh: 40"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('jumlah_kursi') border-red-400 @enderror">
                @error('jumlah_kursi')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
                    Perbarui Bus
                </button>
                <a href="{{ route('admin.bus.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
