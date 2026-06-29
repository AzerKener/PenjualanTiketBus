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
            ->where(function ($q) {
                $q->where('sales_id', Auth::id())
                  ->orWhere('tipe_pemesanan', 'Online');
            })
            ->orderByDesc('tanggal_transaksi');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            // Jika search berupa angka (mungkin ID pesanan), hilangkan prefix 0
            $searchId = ltrim($search, '0');
            $query->where(function ($q) use ($search, $searchId) {
                $q->where('id', $searchId)
                  ->orWhere('nama_pemesan', 'like', "%{$search}%")
                  ->orWhere('no_hp_pemesan', 'like', "%{$search}%");
            });
        }

        $transaksis = $query->paginate(15)->withQueryString();

        return view('sales.transaksi.index', compact('transaksis'));
    }

    public function konfirmasi(Pemesanan $pemesanan)
    {
        $pemesanan->update([
            'status_pembayaran' => 'lunas',
            'sales_id' => Auth::id() // Assign to the sales who confirmed it
        ]);
        
        // Kirim notifikasi E-Tiket via WhatsApp
        if ($pemesanan->no_hp_pemesan) {
            $twilio = app(\App\Services\TwilioService::class);
            $message = "Terima kasih, pembayaran tiket #" . str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) . " Anda telah BERHASIL diverifikasi.\n\n";
            $message .= "Berikut adalah tautan E-Tiket Anda:\n";
            $message .= route('user.etiket', $pemesanan->id) . "\n\n";
            $message .= "Silakan tunjukkan E-Tiket ini kepada petugas saat keberangkatan.";

            $twilio->sendWhatsAppMessage($pemesanan->no_hp_pemesan, $message);
        }

        return back()->with('success', 'Pembayaran tiket #' . str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) . ' berhasil dikonfirmasi menjadi Lunas.');
    }
}
