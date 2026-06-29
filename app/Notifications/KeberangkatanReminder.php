<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;
use Carbon\Carbon;

class KeberangkatanReminder extends Notification
{
    use Queueable;

    public $jadwal;
    public $jenis; // 'H-1', 'H-3h', 'H-1h'
    public $untuk; // 'supir', 'penumpang'

    public function __construct(Jadwal $jadwal, $jenis, $untuk)
    {
        $this->jadwal = $jadwal;
        $this->jenis = $jenis;
        $this->untuk = $untuk;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Menyimpan ke database saja
    }

    public function toDatabase(object $notifiable): array
    {
        $waktuStr = Carbon::parse($this->jadwal->tanggal_berangkat . ' ' . $this->jadwal->waktu_berangkat)->isoFormat('D MMMM Y, HH:mm');
        $ruteStr = $this->jadwal->rute->asal . ' ke ' . $this->jadwal->rute->tujuan;

        $pesan = '';
        if ($this->untuk === 'supir') {
            $pesan = "Pengingat Tugas: Anda memiliki jadwal keberangkatan untuk rute {$ruteStr} pada {$waktuStr}. Bus: {$this->jadwal->bus->nomor_polisi}.";
        } else {
            $pesan = "Pengingat Perjalanan: Bus Anda menuju {$this->jadwal->rute->tujuan} akan berangkat pada {$waktuStr} dari {$this->jadwal->pool->nama_pool}. Mohon bersiap.";
        }

        return [
            'judul' => 'Pengingat Keberangkatan (' . $this->jenis . ')',
            'pesan' => $pesan,
            'jadwal_id' => $this->jadwal->id,
            'icon' => 'clock',
            'color' => 'blue'
        ];
    }
}
