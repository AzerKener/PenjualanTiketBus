<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pemesanan;

class PesananBaruAdmin extends Notification
{
    use Queueable;

    public $pemesanan;

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $formattedPrice = number_format($this->pemesanan->total_bayar, 0, ',', '.');
        $message = 'Ada pesanan baru via ' . $this->pemesanan->metode_pembayaran . ' dari ' . $this->pemesanan->nama_pemesan . '. ';
        
        if (in_array($this->pemesanan->metode_pembayaran, ['Transfer', 'E-Wallet'])) {
            $message .= 'Mohon pastikan user sudah membayar atau mentransfer dana sebesar Rp ' . $formattedPrice . '.';
        } else {
            $message .= 'Mohon segera verifikasi.';
        }

        return [
            'type' => 'pesanan_baru_admin',
            'judul' => 'Pesanan Baru Masuk',
            'pesan' => $message,
            'pemesanan_id' => $this->pemesanan->id,
            'icon' => 'banknotes',
            'color' => 'blue'
        ];
    }
}
