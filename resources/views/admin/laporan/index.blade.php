@extends('layouts.admin')
@section('page-title', 'Laporan per Pool')
@section('page-subtitle', 'Filter rute, bus, dan penumpang berdasarkan pool')

@section('content')
{{-- Filter --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('admin.laporan.index') }}"
        class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Pool</label>
            <select name="pool_id"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[180px]">
                <option value="">Semua Pool</option>
                @foreach($pools as $pool)
                    <option value="{{ $pool->id }}" {{ request('pool_id') == $pool->id ? 'selected' : '' }}>
                        {{ $pool->nama_pool }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai</label>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                Tampilkan
            </button>
            <a href="{{ route('admin.laporan.index') }}"
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

@if($jadwals->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    <p class="text-slate-500 font-medium">Tidak ada data jadwal ditemukan</p>
    <p class="text-slate-400 text-sm mt-1">Coba sesuaikan filter pencarian Anda.</p>
</div>
@else

{{-- Ringkasan Umum --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
        <p class="text-2xl font-bold text-slate-800">{{ $ringkasan['total_jadwal'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Jadwal</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
        <p class="text-2xl font-bold text-slate-800">{{ $ringkasan['total_penumpang'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Penumpang</p>
    </div>
    <div class="bg-green-50 rounded-xl border border-green-100 p-4 text-center shadow-sm col-span-2 md:col-span-1">
        <p class="text-lg font-bold text-green-700">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</p>
        <p class="text-xs text-green-500 mt-1">Total Pendapatan</p>
    </div>
</div>

{{-- Group by Pool → Rute + Bus --}}
@foreach($jadwals->groupBy('pool_id') as $poolId => $jadwalPerPool)
@php $pool = $jadwalPerPool->first()->pool; @endphp
<div class="mb-8">
    {{-- Pool Header --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Pool</p>
            <h2 class="text-xl font-bold text-white">{{ $pool->nama_pool }}</h2>
            <p class="text-slate-400 text-sm">{{ $pool->lokasi }}</p>
        </div>
        <div class="text-right">
            <p class="text-slate-400 text-xs">{{ $jadwalPerPool->count() }} jadwal</p>
            <p class="text-white font-bold">{{ $jadwalPerPool->sum(fn($j) => $j->penumpangs->count()) }} penumpang</p>
        </div>
    </div>

    {{-- Group by Rute + Bus --}}
    @foreach($jadwalPerPool->groupBy(fn($j) => $j->rute_id . '-' . $j->bus_id) as $key => $jadwalGroup)
    @php
        $sample = $jadwalGroup->first();
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-4 overflow-hidden">
        {{-- Rute + Bus Header --}}
        <div class="bg-blue-50 border-b border-blue-100 px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <div class="flex items-center gap-3">
                <div>
                    <span class="text-blue-700 font-semibold">{{ $sample->rute->asal }} → {{ $sample->rute->tujuan }}</span>
                    <span class="mx-2 text-blue-300">|</span>
                    <span class="font-mono text-sm text-blue-600">{{ $sample->bus->nomor_polisi }}</span>
                    <span class="ml-1 text-xs text-blue-400">({{ $sample->bus->tipe_bus }}, {{ $sample->bus->jumlah_kursi }} kursi)</span>
                </div>
            </div>
            <span class="text-xs text-blue-600 font-medium">{{ $jadwalGroup->count() }} jadwal</span>
        </div>

        {{-- Jadwal List --}}
        @foreach($jadwalGroup as $jadwal)
        <div class="border-b border-slate-100 last:border-b-0">
            {{-- Jadwal Row --}}
            <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 hover:bg-slate-50 cursor-pointer"
                onclick="togglePenumpang('pnp-{{ $jadwal->id }}', this)">
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="font-medium text-slate-700">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}
                        {{ substr($jadwal->waktu_berangkat,0,5) }}
                    </span>
                    @if($jadwal->status === 'menunggu')
                        <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">Menunggu</span>
                    @elseif($jadwal->status === 'berangkat')
                        <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-600">Berangkat</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-600">Selesai</span>
                    @endif
                    <span class="text-slate-500">Supir: <strong>{{ $jadwal->supir1->nama }}</strong></span>
                    @if($jadwal->kenek)<span class="text-slate-500">Kenek: {{ $jadwal->kenek->nama }}</span>@endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-700">
                        {{ $jadwal->penumpangs->count() }}/{{ $jadwal->bus->jumlah_kursi }} penumpang
                    </span>
                    <svg class="w-4 h-4 text-slate-400 toggle-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            {{-- Penumpang Collapsible --}}
            <div id="pnp-{{ $jadwal->id }}" class="hidden bg-slate-50 border-t border-slate-100 px-6 py-3">
                @if($jadwal->penumpangs->isEmpty())
                    <p class="text-slate-400 text-sm text-center py-2">Belum ada penumpang</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="pb-2 font-semibold pr-4">No. Kursi</th>
                                    <th class="pb-2 font-semibold pr-4">Nama Penumpang</th>
                                    <th class="pb-2 font-semibold">Tipe Pemesanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($jadwal->penumpangs as $pnp)
                                <tr>
                                    <td class="py-1.5 pr-4 font-mono font-bold text-slate-700">{{ $pnp->nomor_kursi }}</td>
                                    <td class="py-1.5 pr-4 text-slate-700">{{ $pnp->nama_penumpang }}</td>
                                    <td class="py-1.5">
                                        @if($pnp->pemesanan && $pnp->pemesanan->tipe_pemesanan === 'Online')
                                            <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-600">Online</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-600">Sales Pool</span>
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
        @endforeach
    </div>
    @endforeach
</div>
@endforeach
@endif

<script>
function togglePenumpang(id, row) {
    const el = document.getElementById(id);
    const icon = row.querySelector('.toggle-icon');
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        el.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection
