<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;
use Carbon\Carbon;

class JadwalSelanjutnya extends Notification
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
        $waktuStr = Carbon::parse($this->jadwal->tanggal_berangkat . ' ' . $this->jadwal->waktu_berangkat)->isoFormat('D MMMM Y, HH:mm');
        $ruteStr = $this->jadwal->rute->asal . ' ke ' . $this->jadwal->rute->tujuan;

        return [
            'judul' => 'Jadwal Tugas Selanjutnya',
            'pesan' => "Tugas Anda berikutnya adalah rute {$ruteStr} pada {$waktuStr} dari {$this->jadwal->pool->nama_pool}. Harap persiapkan armada dengan baik.",
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'calendar',
            'color' => 'amber'
        ];
    }
}
