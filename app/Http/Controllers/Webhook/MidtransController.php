<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use Midtrans\Config;
use Midtrans\Notification;
use App\Services\TwilioService;

class MidtransController extends Controller
{
    public function webhook(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $notification->transaction_status;
        $orderIdParts = explode('-', $notification->order_id);
        $pemesananId = $orderIdParts[0];

        $pemesanan = Pemesanan::find($pemesananId);

        if (!$pemesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($pemesanan->status_pembayaran !== 'lunas') {
                $pemesanan->update(['status_pembayaran' => 'lunas']);

                if ($pemesanan->user) {
                    $pemesanan->user->notify(new \App\Notifications\UpdateStatusTiket($pemesanan, 'lunas'));
                }

                // Kirim notifikasi WA E-Ticket
                if ($pemesanan->no_hp_pemesan) {
                    $twilio = app(TwilioService::class);
                    $jadwalStr = $pemesanan->jadwal->rute->asal . ' ke ' . $pemesanan->jadwal->rute->tujuan;
                    $linkTiket = route('user.etiket', $pemesanan->id);
                    
                    $message = "Halo {$pemesanan->nama_pemesan},\n\n";
                    $message .= "Pembayaran tiket Anda sebesar Rp " . number_format($pemesanan->total_bayar, 0, ',', '.') . " telah kami terima.\n";
                    $message .= "Rute: {$jadwalStr}\n";
                    $message .= "E-Ticket Anda sudah dapat digunakan.\n\n";
                    $message .= "Klik link berikut untuk melihat E-Ticket:\n{$linkTiket}";
                    
                    $twilio->sendWhatsAppMessage($pemesanan->no_hp_pemesan, $message);
                }
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $pemesanan->update(['status_pembayaran' => 'batal']);
            
            // Hapus data penumpang agar kursi kembali kosong
            $pemesanan->penumpangs()->delete();

            if ($pemesanan->user) {
                $pemesanan->user->notify(new \App\Notifications\UpdateStatusTiket($pemesanan, 'dibatalkan', 'Pembayaran kadaluarsa atau dibatalkan otomatis oleh sistem.'));
            }
        } else if ($transactionStatus == 'pending') {
            $pemesanan->update(['status_pembayaran' => 'pending']);
        }

        return response()->json(['message' => 'Success']);
    }
}
