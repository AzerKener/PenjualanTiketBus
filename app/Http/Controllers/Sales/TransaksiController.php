<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    /**
     * Tampilkan semua pemesanan yang dibuat oleh sales yang sedang login,
     * dengan filter tanggal opsional.
     * Load relasi: jadwal.rute, jadwal.bus, penumpangs.
     */
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_dari'  => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
            'status_pembayaran' => 'nullable|in:pending,lunas,gagal',
        ]);

        // Pool sales hanya melihat transaksi yang dia buat sendiri
        $query = Pemesanan::with([
                'jadwal.rute',
                'jadwal.bus',
                'jadwalPulang.rute',
                'jadwalPulang.bus',
                'penumpangs',
            ])
            ->where('sales_id', Auth::id())
            ->where('tipe_pemesanan', 'Sales_Pool');

        // Filter tanggal transaksi
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        // Filter status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $transaksis = $query->orderByDesc('tanggal_transaksi')->paginate(15)->withQueryString();

        // Statistik ringkasan untuk sales yang login
        $totalTransaksi   = Pemesanan::where('sales_id', Auth::id())->where('tipe_pemesanan', 'Sales_Pool')->count();
        $totalPendapatan  = Pemesanan::where('sales_id', Auth::id())->where('tipe_pemesanan', 'Sales_Pool')->where('status_pembayaran', 'lunas')->sum('total_bayar');
        $transaksiPending = Pemesanan::where('sales_id', Auth::id())->where('tipe_pemesanan', 'Sales_Pool')->where('status_pembayaran', 'pending')->count();

        return view('sales.transaksi.index', compact(
            'transaksis',
            'totalTransaksi',
            'totalPendapatan',
            'transaksiPending'
        ));
    }

    /**
     * Tampilkan detail satu transaksi.
     * Sales hanya boleh melihat transaksinya sendiri.
     */
    public function show(int $id)
    {
        $transaksi = Pemesanan::with([
                'jadwal.rute',
                'jadwal.bus',
                'jadwal.pool',
                'jadwalPulang.rute',
                'jadwalPulang.bus',
                'penumpangsPergi',
                'penumpangsPulang',
                'sales',
            ])
            ->where('sales_id', Auth::id())
            ->findOrFail($id);

        return view('sales.transaksi.show', compact('transaksi'));
    }

    /**
     * Konfirmasi pembayaran Transfer / E-Wallet menjadi 'lunas'.
     * Hanya bisa dilakukan oleh sales yang membuat transaksi tersebut.
     */
    public function konfirmasi(int $id)
    {
        $transaksi = Pemesanan::where('sales_id', Auth::id())
            ->where('status_pembayaran', 'pending')
            ->findOrFail($id);

        $transaksi->update(['status_pembayaran' => 'lunas']);

        return redirect()
            ->route('sales.transaksi.show', $transaksi->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi. Status transaksi sekarang: Lunas.');
    }
}
