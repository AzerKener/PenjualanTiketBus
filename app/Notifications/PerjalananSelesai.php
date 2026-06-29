<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;

class PerjalananSelesai extends Notification
{
    use Queueable;

    public $jadwal;

    public function __construct(Jadwal $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $ruteStr = $this->jadwal->rute->asal . ' ke ' . $this->jadwal->rute->tujuan;

        return [
            'judul' => 'Perjalanan Selesai',
            'pesan' => "Terima kasih telah menggunakan layanan kami untuk perjalanan {$ruteStr}. Semoga perjalanan Anda menyenangkan dan sampai jumpa di perjalanan berikutnya!",
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'check-circle',
            'color' => 'green'
        ];
    }
}
