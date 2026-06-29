@extends('layouts.supir')
@section('title', 'Detail Jadwal')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('supir.jadwal.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Detail Perjalanan</h1>
        <p class="text-slate-500 mt-1">Kelola status perjalanan dan penumpang bus Anda.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Info --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Status Update Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Status Perjalanan</h2>
            
            <div class="flex items-center gap-3 mb-6">
                <div>
                    @if($jadwal->status === 'menunggu')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-700">
                            <span class="w-2 h-2 bg-slate-500 rounded-full"></span>Menunggu Penumpang
                        </span>
                    @elseif($jadwal->status === 'boarding')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-700">
                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>Proses Boarding (Penumpang Naik)
                        </span>
                    @elseif($jadwal->status === 'berangkat')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>Sedang Dalam Perjalanan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>Telah Tiba di Tujuan
                        </span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('supir.jadwal.updateStatus', $jadwal->id) }}" method="POST" class="flex flex-wrap gap-3">
                @csrf
                @method('PATCH')
                
                @if($jadwal->status === 'menunggu')
                    <button type="submit" name="status" value="boarding" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                        Mulai Boarding
                    </button>
                @elseif($jadwal->status === 'boarding')
                    <button type="submit" name="status" value="berangkat" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                        Bus Berangkat
                    </button>
                @elseif($jadwal->status === 'berangkat')
                    <button type="submit" name="status" value="tiba" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                        Tiba di Tujuan
                    </button>
                @else
                    <div class="flex-1 bg-slate-100 text-slate-500 font-semibold py-3 px-4 rounded-xl text-center border border-slate-200">
                        Perjalanan Telah Selesai
                    </div>
                @endif
            </form>
        </div>

        {{-- Daftar Penumpang --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800">Daftar Penumpang</h2>
                <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">
                    {{ $penumpangs->count() }} Orang
                </span>
            </div>

            @if($penumpangs->isEmpty())
                <div class="text-center py-8">
                    <p class="text-slate-500">Belum ada penumpang pada jadwal ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 bg-slate-50 uppercase border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. Kursi</th>
                                <th class="px-4 py-3 font-semibold">Nama Penumpang</th>
                                <th class="px-4 py-3 font-semibold">Tipe Pesanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($penumpangs as $pnp)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-700">
                                        {{ $pnp->nomor_kursi }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">
                                        {{ $pnp->nama_penumpang }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($pnp->pemesanan && $pnp->pemesanan->tipe_pemesanan === 'Online')
                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-600 text-xs font-medium">Online</span>
                                        @else
                                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-600 text-xs font-medium">Loket/Sales</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Informasi Rute & Armada</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Rute</p>
                    <p class="font-semibold text-slate-800">{{ $jadwal->rute->asal }} &rarr; {{ $jadwal->rute->tujuan }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Keberangkatan (Pool Asal)</p>
                    <p class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }} - {{ substr($jadwal->waktu_berangkat, 0, 5) }}</p>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $jadwal->pool->nama_pool ?? '-' }}</p>
                </div>
                @if($jadwal->poolTujuan)
                <div>
                    <p class="text-xs text-slate-500 mb-1">Kedatangan (Pool Tujuan)</p>
                    <p class="text-sm text-slate-500">{{ $jadwal->poolTujuan->nama_pool }}</p>
                </div>
                @endif
                <hr class="border-slate-100">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Armada Bus</p>
                    <p class="font-semibold text-slate-800">{{ $jadwal->bus->nomor_polisi }}</p>
                    <p class="text-sm text-slate-500">{{ $jadwal->bus->tipe_bus }} ({{ $jadwal->bus->jumlah_kursi }} Kursi)</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Kru Bertugas</h3>
            
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $jadwal->supir1->nama ?? '-' }}</p>
                        <p class="text-xs text-slate-500">Supir Utama</p>
                    </div>
                </div>
                
                @if($jadwal->supir2)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $jadwal->supir2->nama }}</p>
                        <p class="text-xs text-slate-500">Supir Cadangan</p>
                    </div>
                </div>
                @endif
                
                @if($jadwal->kenek)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $jadwal->kenek->nama }}</p>
                        <p class="text-xs text-slate-500">Kenek</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
