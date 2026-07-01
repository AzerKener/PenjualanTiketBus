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
        $poolId = Auth::user()->pool_id;
        $hariIni = now()->toDateString();

        $baseQuery = Pemesanan::whereHas('jadwal', function($q) use ($poolId) {
            $q->where('pool_id', $poolId);
        })->where(function ($q) use ($salesId) {
            $q->where('sales_id', $salesId)
              ->orWhere('tipe_pemesanan', 'Online');
        });

        $penjualanHariIni = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', $hariIni)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_bayar');

        $tiketHariIni = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', $hariIni)
            ->withCount('penumpangs')
            ->get()->sum('penumpangs_count');

        $totalPenjualan = (clone $baseQuery)->where('status_pembayaran', 'lunas')->sum('total_bayar');

        $totalTiket = (clone $baseQuery)
            ->withCount('penumpangs')
            ->get()->sum('penumpangs_count');

        $transaksiTerbaru = (clone $baseQuery)->with(['jadwal.rute'])
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
