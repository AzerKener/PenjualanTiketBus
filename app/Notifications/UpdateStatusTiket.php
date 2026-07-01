<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pemesanan;

class UpdateStatusTiket extends Notification
{
    use Queueable;

    public $pemesanan;
    public $status;
    public $pesanTambahan;

    public function __construct(Pemesanan $pemesanan, $status, $pesanTambahan = '')
    {
        $this->pemesanan = $pemesanan;
        $this->status = $status;
        $this->pesanTambahan = $pesanTambahan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $judul = 'Status Tiket Diperbarui';
        $icon = 'bell';
        $color = 'blue';

        if ($this->status == 'lunas' || $this->status == 'menunggu') {
            $judul = 'Tiket Berhasil Dibayar';
            $icon = 'check-circle';
            $color = 'green';
            $pesan = "Pembayaran untuk tiket Anda ke {$this->pemesanan->jadwal->rute->tujuan} telah lunas. Tiket Anda sudah aktif.";
        } else if ($this->status == 'dibatalkan') {
            $judul = 'Tiket Dibatalkan';
            $icon = 'times-circle'; // use a generic bell if not exist
            $color = 'red';
            $pesan = "Pemesanan tiket Anda ke {$this->pemesanan->jadwal->rute->tujuan} telah dibatalkan. " . $this->pesanTambahan;
        } else {
            $pesan = "Status tiket Anda telah berubah menjadi: {$this->status}.";
        }

        return [
            'judul' => $judul,
            'pesan' => $pesan,
            'pemesanan_id' => $this->pemesanan->id,
            'icon' => $icon,
            'color' => $color
        ];
    }
}
