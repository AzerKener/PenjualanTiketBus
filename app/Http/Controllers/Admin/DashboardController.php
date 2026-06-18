<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalBus      = Bus::count();
        $totalJadwal   = Jadwal::count();

        $pemesananHariIni = Pemesanan::whereDate('tanggal_transaksi', $today)->count();

        $totalPendapatanHariIni = Pemesanan::whereDate('tanggal_transaksi', $today)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_bayar');

        $jadwalHariIni = Jadwal::whereDate('tanggal_berangkat', $today)->count();

        $stats = [
            'total_bus' => $totalBus,
            'jadwal_hari_ini' => $jadwalHariIni,
            'transaksi_hari_ini' => $pemesananHariIni,
            'pendapatan_hari_ini' => $totalPendapatanHariIni,
        ];

        $jadwalTerkini = Jadwal::with(['rute', 'bus', 'pool'])
            ->whereDate('tanggal_berangkat', $today)
            ->orderBy('waktu_berangkat')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'jadwalTerkini'
        ));
    }
}
