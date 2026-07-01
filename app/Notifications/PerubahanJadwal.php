<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;
use Carbon\Carbon;

class PerubahanJadwal extends Notification
{
    use Queueable;

    public $jadwal;
    public $perubahan;

    public function __construct(Jadwal $jadwal, $perubahan = '')
    {
        $this->jadwal = $jadwal;
        $this->perubahan = $perubahan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $waktu = Carbon::parse($this->jadwal->tanggal_berangkat . ' ' . $this->jadwal->waktu_berangkat)->isoFormat('D MMMM Y, HH:mm');
        return [
            'judul' => 'Perubahan Jadwal Keberangkatan',
            'pesan' => "Terdapat perubahan pada jadwal keberangkatan Anda ke {$this->jadwal->rute->tujuan}. Jadwal terbaru adalah: {$waktu}. " . $this->perubahan,
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'calendar',
            'color' => 'amber'
        ];
    }
}
