<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $pools = Pool::orderBy('nama_pool')->get();

        $query = Pemesanan::with([
                'jadwal.bus',
                'jadwal.rute',
                'jadwal.pool',
                'jadwalPulang.rute',
                'user',
                'penumpangs',
            ])
            ->when($request->filled('pool_id'), function ($q) use ($request) {
                $q->whereHas('jadwal', fn ($j) => $j->where('pool_id', $request->pool_id));
            })
            ->when($request->filled('tanggal_mulai'), function ($q) use ($request) {
                $q->whereDate('tanggal_transaksi', '>=', $request->tanggal_mulai);
            })
            ->when($request->filled('tanggal_akhir'), function ($q) use ($request) {
                $q->whereDate('tanggal_transaksi', '<=', $request->tanggal_akhir);
            })
            ->when($request->filled('status_pembayaran'), function ($q) use ($request) {
                $q->where('status_pembayaran', $request->status_pembayaran);
            })
            ->when($request->filled('tipe_pemesanan'), function ($q) use ($request) {
                $q->where('tipe_pemesanan', $request->tipe_pemesanan);
            })
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('nama_pemesan', 'like', '%' . $keyword . '%')
                        ->orWhere('no_hp_pemesan', 'like', '%' . $keyword . '%');
                });
            })
            ->latest('tanggal_transaksi');

        $pemesanans = $query->paginate(15)->withQueryString();

        // Total pendapatan dari hasil filter
        $totalPendapatan = Pemesanan::when($request->filled('pool_id'), function ($q) use ($request) {
                $q->whereHas('jadwal', fn ($j) => $j->where('pool_id', $request->pool_id));
            })
            ->when($request->filled('tanggal_mulai'), fn ($q) => $q->whereDate('tanggal_transaksi', '>=', $request->tanggal_mulai))
            ->when($request->filled('tanggal_akhir'), fn ($q) => $q->whereDate('tanggal_transaksi', '<=', $request->tanggal_akhir))
            ->when($request->filled('status_pembayaran'), fn ($q) => $q->where('status_pembayaran', $request->status_pembayaran))
            ->where('status_pembayaran', 'lunas')
            ->sum('total_bayar');

        $totalTransaksi   = $pemesanans->total();
        $transaksiPending = Pemesanan::where('status_pembayaran', 'pending')->count();
        $transaksiLunas   = Pemesanan::where('status_pembayaran', 'lunas')->count();

        return view('admin.transaksi.index', compact(
            'pemesanans',
            'pools',
            'totalPendapatan',
            'totalTransaksi',
            'transaksiPending',
            'transaksiLunas'
        ));
    }

    public function konfirmasi(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status_pembayaran' => 'lunas']);
        
        // Kirim notifikasi E-Tiket via WhatsApp
        if ($pemesanan->no_hp_pemesan) {
            $twilio = app(\App\Services\TwilioService::class);
            $message = "Terima kasih, pembayaran tiket #" . str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) . " Anda telah BERHASIL diverifikasi oleh Admin.\n\n";
            $message .= "Berikut adalah tautan E-Tiket Anda:\n";
            $message .= route('user.etiket', $pemesanan->id) . "\n\n";
            $message .= "Silakan tunjukkan E-Tiket ini kepada petugas saat keberangkatan.";

            $twilio->sendWhatsAppMessage($pemesanan->no_hp_pemesan, $message);
        }

        return back()->with('success', 'Pembayaran transaksi #' . $pemesanan->id . ' berhasil dikonfirmasi menjadi Lunas.');
    }
}
