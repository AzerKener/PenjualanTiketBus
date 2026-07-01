<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;
use Carbon\Carbon;

class KedatanganReminder extends Notification
{
    use Queueable;

    public $jadwal;
    public $waktuTiba;

    public function __construct(Jadwal $jadwal, $waktuTiba)
    {
        $this->jadwal = $jadwal;
        $this->waktuTiba = $waktuTiba; // Format datetime
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $estimasi = Carbon::parse($this->waktuTiba)->isoFormat('HH:mm');
        return [
            'judul' => 'Pengingat Kedatangan',
            'pesan' => "Bus Anda akan tiba di tujuan ({$this->jadwal->rute->tujuan}) dalam 1 jam. Estimasi tiba pada pukul {$estimasi}. Mohon bersiap dan periksa kembali barang bawaan Anda.",
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'clock',
            'color' => 'blue'
        ];
    }
}
