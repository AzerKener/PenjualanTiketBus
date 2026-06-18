@extends('layouts.admin')
@section('page-title', 'Data Penumpang')
@section('page-subtitle', 'Hanya tersedia setelah bus berangkat')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-5 p-5">
    <form method="GET" action="{{ route('admin.penumpang.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Jadwal</label>
            <select name="jadwal_id" id="jadwal_id"
                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Jadwal --</option>
                @foreach($jadwals as $j)
                    <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->rute->asal }} → {{ $j->rute->tujuan }} |
                        {{ \Carbon\Carbon::parse($j->tanggal_berangkat)->isoFormat('D MMM Y') }}
                        {{ substr($j->waktu_berangkat, 0, 5) }} |
                        {{ $j->bus->nomor_polisi }} ({{ $j->status }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit"
                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition-colors">
                Tampilkan
            </button>
        </div>
    </form>
</div>

@if(!request('jadwal_id'))
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-8 text-center">
        <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="text-blue-700 font-medium">Pilih jadwal terlebih dahulu</p>
        <p class="text-blue-500 text-sm mt-1">Data penumpang akan ditampilkan setelah jadwal dipilih dan bus sudah berangkat.</p>
    </div>

@elseif(isset($pesanStatus))
    {{-- Bus belum berangkat --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
        <svg class="w-12 h-12 text-amber-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-amber-800 font-semibold">{{ $pesanStatus }}</p>
        <p class="text-amber-600 text-sm mt-1">
            Data penumpang hanya dapat dilihat setelah status bus berubah menjadi <strong>Berangkat</strong>.
        </p>
        @if(isset($jadwal))
        <div class="mt-4 inline-flex items-center gap-2 text-sm text-amber-700">
            <span>Status saat ini:</span>
            <span class="px-2.5 py-0.5 bg-amber-100 border border-amber-300 rounded-full font-semibold capitalize">
                {{ $jadwal->status }}
            </span>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.jadwal.index') }}"
                class="text-sm text-blue-600 hover:underline">→ Ubah status di halaman Jadwal</a>
        </div>
        @endif
    </div>

@else
    {{-- Info Jadwal --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="font-semibold text-slate-800">Info Jadwal</h2>
            @if($jadwal->status === 'berangkat')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>Sedang Berangkat
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Selesai
                </span>
            @endif
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-slate-500">Rute</p>
                <p class="font-semibold text-slate-800">{{ $jadwal->rute->asal }} → {{ $jadwal->rute->tujuan }}</p>
            </div>
            <div>
                <p class="text-slate-500">Bus</p>
                <p class="font-semibold text-slate-800 font-mono">{{ $jadwal->bus->nomor_polisi }}</p>
                <p class="text-xs text-slate-400">{{ $jadwal->bus->tipe_bus }}</p>
            </div>
            <div>
                <p class="text-slate-500">Tanggal & Jam</p>
                <p class="font-semibold text-slate-800">
                    {{ \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('D MMM Y') }}
                    {{ substr($jadwal->waktu_berangkat, 0, 5) }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Supir & Kenek</p>
                <p class="font-semibold text-slate-800">{{ $jadwal->supir1->nama }}</p>
                @if($jadwal->supir2)<p class="text-xs text-slate-500">{{ $jadwal->supir2->nama }}</p>@endif
                @if($jadwal->kenek)<p class="text-xs text-slate-400">Kenek: {{ $jadwal->kenek->nama }}</p>@endif
            </div>
        </div>
    </div>

    {{-- Tabel Penumpang --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Daftar Penumpang</h2>
            <span class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $penumpangs->count() }}</strong> penumpang</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Kursi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Penumpang</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemesanan ID</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Pemesan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penumpangs as $i => $p)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-3">
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $p->nomor_kursi }}</span>
                        </td>
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $p->nama_penumpang }}</td>
                        <td class="px-6 py-3 font-mono text-slate-500">#{{ $p->pemesanan_id }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $p->pemesanan->nama_pemesan ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @if($p->pemesanan && $p->pemesanan->tipe_pemesanan === 'Online')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Online</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Sales Pool</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            Belum ada penumpang terdaftar untuk jadwal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
