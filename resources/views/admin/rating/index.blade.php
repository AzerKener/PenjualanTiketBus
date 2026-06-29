@extends('layouts.admin')
@section('page-title', 'Rating & Ulasan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Rating & Ulasan</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau feedback dan ulasan dari penumpang</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                <span class="text-yellow-400 text-lg">⭐</span>
                <span class="font-bold text-slate-800">{{ number_format($averageRating, 1) }}</span>
                <span class="text-xs text-slate-500">/ 5.0 ({{ $totalRatings }} ulasan)</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h2 class="font-semibold text-slate-700">Daftar Ulasan</h2>
            
            <form action="{{ route('admin.rating.index') }}" method="GET" class="flex items-center gap-2">
                <select name="rating" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Rating</option>
                    <option value="5" {{ $request->rating == '5' ? 'selected' : '' }}>5 Bintang</option>
                    <option value="4" {{ $request->rating == '4' ? 'selected' : '' }}>4 Bintang</option>
                    <option value="3" {{ $request->rating == '3' ? 'selected' : '' }}>3 Bintang</option>
                    <option value="2" {{ $request->rating == '2' ? 'selected' : '' }}>2 Bintang</option>
                    <option value="1" {{ $request->rating == '1' ? 'selected' : '' }}>1 Bintang</option>
                </select>
            </form>
        </div>
        
        <div class="divide-y divide-slate-100">
            @forelse($ratings as $rating)
            <div class="p-5 hover:bg-slate-50 transition-colors">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-slate-800">{{ $rating->user->name }}</span>
                            <span class="text-xs text-slate-400">• {{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex text-yellow-400 text-sm mb-2">
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $rating->rating)
                                    <span>★</span>
                                @else
                                    <span class="text-slate-200">★</span>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-slate-600 italic">
                            {{ $rating->ulasan ?? '(Tidak ada ulasan teks)' }}
                        </p>
                    </div>
                    
                    <div class="sm:text-right border-t sm:border-t-0 sm:border-l border-slate-100 pt-3 sm:pt-0 sm:pl-4 mt-3 sm:mt-0">
                        <p class="text-xs font-bold text-slate-700">Perjalanan:</p>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $rating->jadwal->rute->asal }} &rarr; {{ $rating->jadwal->rute->tujuan }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($rating->jadwal->tanggal_berangkat)->format('d M Y') }} • {{ $rating->jadwal->bus->nomor_polisi }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-500">
                Belum ada ulasan yang diberikan.
            </div>
            @endforelse
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $ratings->links() }}
        </div>
    </div>
</div>
@endsection
