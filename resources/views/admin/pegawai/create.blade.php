@extends('layouts.admin')

@section('page-title', 'Tambah Pegawai')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pegawai.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Pegawai</h1>
            <p class="text-sm text-slate-500 mt-0.5">Daftarkan pegawai baru</p>
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

        <form action="{{ route('admin.pegawai.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nama"
                       name="nama"
                       value="{{ old('nama') }}"
                       placeholder="Contoh: Budi Santoso"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('nama') border-red-400 @enderror">
                @error('nama')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 mb-1">
                    Role / Jabatan <span class="text-red-500">*</span>
                </label>
                <select id="role"
                        name="role"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white @error('role') border-red-400 @enderror">
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="Supir" {{ old('role') == 'Supir' ? 'selected' : '' }}>Supir</option>
                    <option value="Kenek" {{ old('role') == 'Kenek' ? 'selected' : '' }}>Kenek</option>
                    <option value="Sales" {{ old('role') == 'Sales' ? 'selected' : '' }}>Sales</option>
                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pool --}}
            <div>
                <label for="pool_id" class="block text-sm font-medium text-slate-700 mb-1">
                    Pool <span class="text-red-500">*</span>
                </label>
                <select id="pool_id"
                        name="pool_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white @error('pool_id') border-red-400 @enderror">
                    <option value="" disabled selected>-- Pilih Pool --</option>
                    @foreach($pools as $pool)
                    <option value="{{ $pool->id }}" {{ old('pool_id') == $pool->id ? 'selected' : '' }}>
                        {{ $pool->nama_pool }}
                    </option>
                    @endforeach
                </select>
                @error('pool_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- No. HP --}}
            <div>
                <label for="no_hp" class="block text-sm font-medium text-slate-700 mb-1">
                    Nomor HP
                </label>
                <input type="text"
                       id="no_hp"
                       name="no_hp"
                       value="{{ old('no_hp') }}"
                       placeholder="Contoh: 08123456789"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('no_hp') border-red-400 @enderror">
                @error('no_hp')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
                    Simpan Pegawai
                </button>
                <a href="{{ route('admin.pegawai.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
