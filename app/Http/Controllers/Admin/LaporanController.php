<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\Pool;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $pools = Pool::orderBy('nama_pool')->get();

        $query = Jadwal::with(['bus', 'rute', 'pool', 'penumpangs.pemesanan', 'supir1', 'supir2', 'kenek'])
            ->when($request->filled('pool_id'),      fn ($q) => $q->where('pool_id',  $request->pool_id))
            ->when($request->filled('tanggal_dari'), fn ($q) => $q->whereDate('tanggal_berangkat', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'),fn ($q) => $q->whereDate('tanggal_berangkat', '<=', $request->tanggal_sampai))
            ->orderBy('pool_id')
            ->orderBy('rute_id')
            ->orderBy('bus_id')
            ->orderBy('tanggal_berangkat');

        $jadwals = $query->get();

        $jadwalIds       = $jadwals->pluck('id');
        $totalPendapatan = Pemesanan::whereIn('jadwal_id', $jadwalIds)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_bayar');

        $ringkasan = [
            'total_jadwal'     => $jadwals->count(),
            'total_penumpang'  => $jadwals->sum(fn ($j) => $j->penumpangs->count()),
            'total_pendapatan' => $totalPendapatan,
        ];

        return view('admin.laporan.index', compact('jadwals', 'pools', 'ringkasan'));
    }
}
