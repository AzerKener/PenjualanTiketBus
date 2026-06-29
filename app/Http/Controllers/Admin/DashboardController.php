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

        // Chart 1: Revenue 6 Bulan Terakhir
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenueLabels[] = $month->translatedFormat('M Y');
            $revenueData[] = Pemesanan::whereYear('tanggal_transaksi', $month->year)
                                      ->whereMonth('tanggal_transaksi', $month->month)
                                      ->where('status_pembayaran', 'lunas')
                                      ->sum('total_bayar');
        }

        // Chart 2: Okupansi 7 Hari Terakhir
        $occupancyLabels = [];
        $occupancyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $occupancyLabels[] = $day->translatedFormat('d M');
            
            $jadwalIds = Jadwal::whereDate('tanggal_berangkat', $day->toDateString())->pluck('id');
            if ($jadwalIds->isEmpty()) {
                $occupancyData[] = 0;
            } else {
                $totalKursi = Bus::whereIn('id', Jadwal::whereIn('id', $jadwalIds)->pluck('bus_id'))->sum('jumlah_kursi');
                $terisi = \App\Models\Penumpang::whereIn('jadwal_id', $jadwalIds)->count();
                $occupancyData[] = $totalKursi > 0 ? round(($terisi / $totalKursi) * 100, 1) : 0;
            }
        }

        return view('admin.dashboard', compact(
            'stats',
            'jadwalTerkini',
            'revenueLabels',
            'revenueData',
            'occupancyLabels',
            'occupancyData'
        ));
    }
}
