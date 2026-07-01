<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Jadwal;

class JadwalBerangkatKru extends Notification
{
    use Queueable;

    public $jadwal;

    /**
     * Create a new notification instance.
     */
    public function __construct(Jadwal $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'jadwal_berangkat_kru',
            'title' => 'Waktu Keberangkatan Tiba!',
            'message' => 'Jadwal Bus (Rute ' . $this->jadwal->rute->asal . ' - ' . $this->jadwal->rute->tujuan . ') telah memasuki waktu keberangkatan. Harap segera jalan.',
            'url' => route('notifikasi.index'),
            'icon' => 'truck',
            'color' => 'text-blue-500'
        ];
    }
}
