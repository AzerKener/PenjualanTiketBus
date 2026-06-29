@extends('layouts.admin')

@section('page-title', 'Manajemen Jadwal')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Jadwal</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola jadwal keberangkatan bus</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <form action="{{ route('admin.jadwal.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Filter Pool</label>
                <select name="pool_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white transition">
                    <option value="">Semua Pool</option>
                    @foreach($pools as $pool)
                    <option value="{{ $pool->id }}" {{ request('pool_id') == $pool->id ? 'selected' : '' }}>
                        {{ $pool->nama_pool }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Filter Tanggal</label>
                <input type="date"
                       name="tanggal"
                       value="{{ request('tanggal') }}"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                    Filter
                </button>
                @if(request('pool_id') || request('tanggal'))
                <a href="{{ route('admin.jadwal.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-semibold text-slate-800">Daftar Jadwal</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Bus</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Rute</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Pool</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Jam</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Harga</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Supir 1</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Supir 2</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Kenek</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Status</th>
                        <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono font-semibold text-slate-800">{{ $jadwal->bus->nomor_polisi ?? '-' }}</span>
                            <span class="ml-1.5 text-xs text-slate-500">{{ $jadwal->bus->tipe_bus ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 text-sm text-slate-700">
                                <span>{{ $jadwal->rute->asal ?? '-' }}</span>
                                <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                <span>{{ $jadwal->rute->tujuan ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 text-sm text-slate-700">
                                <span>{{ $jadwal->pool->nama_pool ?? '-' }}</span>
                                <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                <span>{{ $jadwal->poolTujuan->nama_pool ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-mono text-slate-700">
                                {{ \Carbon\Carbon::parse($jadwal->waktu_berangkat)->format('H:i') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            Rp {{ number_format($jadwal->harga_tiket, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $jadwal->supir1->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $jadwal->supir2->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $jadwal->kenek->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $status = $jadwal->status ?? 'menunggu';
                                $badgeClass = match($status) {
                                    'berangkat' => 'bg-blue-100 text-blue-700',
                                    'selesai'   => 'bg-emerald-100 text-emerald-700',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                                $statusLabel = match($status) {
                                    'berangkat' => 'Berangkat',
                                    'selesai'   => 'Selesai',
                                    default     => 'Menunggu',
                                };
                            @endphp
                            <div class="space-y-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                                <form action="{{ route('admin.jadwal.updateStatus', $jadwal->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            onchange="this.form.submit()"
                                            class="w-full border border-slate-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white transition">
                                        <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="berangkat" {{ $status == 'berangkat' ? 'selected' : '' }}>Berangkat</option>
                                        <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                                @if($jadwal->keterangan)
                                    <div class="mt-1 text-[10px] text-red-600 bg-red-50 p-1 rounded border border-red-100 leading-tight">
                                        {{ $jadwal->keterangan }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}"
                                   class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Hapus jadwal ini?')"
                                            class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-slate-400">Belum ada data jadwal</p>
                                <a href="{{ route('admin.jadwal.create') }}" class="text-xs text-blue-600 hover:underline">Tambah jadwal baru</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jadwals->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $jadwals->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
