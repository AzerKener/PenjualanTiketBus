<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $salesId = Auth::id();
        $hariIni = now()->toDateString();

        $penjualanHariIni = Pemesanan::where('sales_id', $salesId)
            ->whereDate('tanggal_transaksi', $hariIni)
            ->sum('total_bayar');

        $tiketHariIni = Pemesanan::where('sales_id', $salesId)
            ->whereDate('tanggal_transaksi', $hariIni)
            ->withCount('penumpangs')
            ->get()->sum('penumpangs_count');

        $totalPenjualan = Pemesanan::where('sales_id', $salesId)->sum('total_bayar');

        $totalTiket = Pemesanan::where('sales_id', $salesId)
            ->withCount('penumpangs')
            ->get()->sum('penumpangs_count');

        $transaksiTerbaru = Pemesanan::with(['jadwal.rute'])
            ->where('sales_id', $salesId)
            ->orderByDesc('tanggal_transaksi')
            ->take(5)
            ->get();

        return view('sales.dashboard.index', compact(
            'penjualanHariIni',
            'tiketHariIni',
            'totalPenjualan',
            'totalTiket',
            'transaksiTerbaru'
        ));
    }
}
