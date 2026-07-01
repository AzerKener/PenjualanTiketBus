<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pemesanan;

class InfoPembayaran extends Notification
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
        $total = number_format($this->pemesanan->total_bayar, 0, ',', '.');
        return [
            'judul' => 'Informasi Pembayaran (Menunggu)',
            'pesan' => "Pemesanan tiket Anda ke {$this->pemesanan->jadwal->rute->tujuan} berhasil dibuat. Silakan selesaikan pembayaran sebesar Rp {$total}.",
            'pemesanan_id' => $this->pemesanan->id,
            'icon' => 'clock',
            'color' => 'amber'
        ];
    }
}
