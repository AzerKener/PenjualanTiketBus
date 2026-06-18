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
@endsection
