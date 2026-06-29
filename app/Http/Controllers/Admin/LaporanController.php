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

    public function exportCsv(Request $request)
    {
        $query = Jadwal::with(['bus', 'rute', 'pool', 'poolTujuan', 'penumpangs', 'supir1', 'supir2', 'kenek'])
            ->when($request->filled('pool_id'),      fn ($q) => $q->where('pool_id',  $request->pool_id))
            ->when($request->filled('tanggal_dari'), fn ($q) => $q->whereDate('tanggal_berangkat', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'),fn ($q) => $q->whereDate('tanggal_berangkat', '<=', $request->tanggal_sampai))
            ->orderBy('pool_id')
            ->orderBy('rute_id')
            ->orderBy('bus_id')
            ->orderBy('tanggal_berangkat');

        $jadwals = $query->get();

        $filename = "laporan_penjualan_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Tanggal Berangkat', 
            'Waktu', 
            'Pool Asal', 
            'Pool Tujuan', 
            'Asal - Tujuan', 
            'Bus', 
            'Supir 1', 
            'Total Penumpang', 
            'Total Pendapatan (Rp)'
        ];

        $callback = function() use($jadwals, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($jadwals as $jadwal) {
                // Hitung pendapatan hanya untuk yang lunas pada jadwal ini
                $pendapatan = Pemesanan::where('jadwal_id', $jadwal->id)
                    ->where('status_pembayaran', 'lunas')
                    ->sum('total_bayar');

                fputcsv($file, [
                    $jadwal->tanggal_berangkat->format('Y-m-d'),
                    \Carbon\Carbon::parse($jadwal->waktu_berangkat)->format('H:i'),
                    $jadwal->pool->nama_pool ?? '-',
                    $jadwal->poolTujuan->nama_pool ?? '-',
                    ($jadwal->rute->asal ?? '-') . ' - ' . ($jadwal->rute->tujuan ?? '-'),
                    $jadwal->bus->nomor_polisi ?? '-',
                    $jadwal->supir1->nama ?? '-',
                    $jadwal->penumpangs->count(),
                    $pendapatan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
