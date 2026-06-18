@extends('layouts.admin')

@section('page-title', 'Edit Rute')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.rute.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Rute</h1>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui rute <span class="font-semibold text-slate-700">{{ $rute->asal }} → {{ $rute->tujuan }}</span></p>
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

        <form action="{{ route('admin.rute.update', $rute->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Asal --}}
            <div>
                <label for="asal" class="block text-sm font-medium text-slate-700 mb-1">
                    Kota Asal <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                    </div>
                    <input type="text"
                           id="asal"
                           name="asal"
                           value="{{ old('asal', $rute->asal) }}"
                           placeholder="Contoh: Yogyakarta"
                           class="w-full border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('asal') border-red-400 @enderror">
                </div>
                @error('asal')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Arrow --}}
            <div class="flex items-center gap-2 px-1">
                <div class="h-px flex-1 bg-slate-200"></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            {{-- Tujuan --}}
            <div>
                <label for="tujuan" class="block text-sm font-medium text-slate-700 mb-1">
                    Kota Tujuan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <div class="w-2 h-2 rounded-full bg-red-400"></div>
                    </div>
                    <input type="text"
                           id="tujuan"
                           name="tujuan"
                           value="{{ old('tujuan', $rute->tujuan) }}"
                           placeholder="Contoh: Jakarta"
                           class="w-full border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('tujuan') border-red-400 @enderror">
                </div>
                @error('tujuan')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
                    Perbarui Rute
                </button>
                <a href="{{ route('admin.rute.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
