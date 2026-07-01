<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;
use Carbon\Carbon;

class BusTibaDiPool extends Notification
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
        $waktuStr = Carbon::parse(Carbon::parse($this->jadwal->tanggal_berangkat)->format('Y-m-d') . ' ' . $this->jadwal->waktu_berangkat)->isoFormat('HH:mm');
        return [
            'judul' => 'Bus Telah Tiba di Pool',
            'pesan' => "Bus Anda (Nopol: {$this->jadwal->bus->nomor_polisi}) menuju {$this->jadwal->rute->tujuan} telah tiba di {$this->jadwal->pool->nama_pool}. Silakan bersiap untuk proses boarding. Keberangkatan pada pukul {$waktuStr}.",
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'check-circle',
            'color' => 'green'
        ];
    }
}
