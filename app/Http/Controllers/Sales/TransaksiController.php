<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemesanan::with(['jadwal.rute', 'penumpangs'])
            ->where('sales_id', Auth::id())
            ->orderByDesc('tanggal_transaksi');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        $transaksis = $query->paginate(15)->withQueryString();

        return view('sales.transaksi.index', compact('transaksis'));
    }
}
