@extends('layouts.user')
@section('title', 'Beri Ulasan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Bagaimana perjalanan Anda?</h1>
            <p class="text-slate-500 text-sm">
                Rute: <strong>{{ $jadwal->rute->asal }} &rarr; {{ $jadwal->rute->tujuan }}</strong> <br>
                Bus: {{ $jadwal->bus->nomor_polisi }} ({{ $jadwal->bus->tipe_bus }})
            </p>
        </div>

        <form action="{{ route('user.rating.store', $jadwal->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div x-data="{ rating: 0, hover: 0 }" class="flex flex-col items-center justify-center">
                <p class="text-sm font-semibold text-slate-700 mb-3">Pilih Bintang</p>
                <div class="flex gap-2">
                    <template x-for="i in 5">
                        <svg @click="rating = i" 
                             @mouseenter="hover = i" 
                             @mouseleave="hover = 0"
                             :class="{ 'text-yellow-400': i <= (hover || rating), 'text-slate-200': i > (hover || rating) }"
                             class="w-12 h-12 cursor-pointer transition-colors" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </template>
                </div>
                <input type="hidden" name="rating" x-model="rating" required>
                @error('rating')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ulasan" class="block text-sm font-medium text-slate-700 mb-2">Ulasan Anda (Opsional)</label>
                <textarea id="ulasan" name="ulasan" rows="4" 
                          placeholder="Ceritakan pengalaman perjalanan Anda bersama kami..."
                          class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('ulasan') }}</textarea>
                @error('ulasan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition shadow-sm">
                Kirim Ulasan
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('user.riwayat') }}" class="text-sm text-slate-500 hover:text-slate-700 transition">Batal</a>
            </div>
        </form>

    </div>
</div>
@endsection
