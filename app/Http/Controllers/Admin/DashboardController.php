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

        // Jadwal hari ini berdasarkan tanggal_berangkat
        $jadwalHariIni = Jadwal::whereDate('tanggal_berangkat', $today)->count();

        // Statistik status jadwal
        $jadwalMenunggu  = Jadwal::where('status', 'menunggu')->count();
        $jadwalBerangkat = Jadwal::where('status', 'berangkat')->count();
        $jadwalSelesai   = Jadwal::where('status', 'selesai')->count();

        // 5 pemesanan terbaru
        $pemesananTerbaru = Pemesanan::with(['jadwal.rute', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBus',
            'totalJadwal',
            'pemesananHariIni',
            'totalPendapatanHariIni',
            'jadwalHariIni',
            'jadwalMenunggu',
            'jadwalBerangkat',
            'jadwalSelesai',
            'pemesananTerbaru'
        ));
    }
}
