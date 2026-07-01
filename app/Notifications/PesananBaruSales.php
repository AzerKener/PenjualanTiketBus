<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pemesanan;

class PesananBaruSales extends Notification
{
    use Queueable;

    public $pemesanan;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
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
            'type' => 'pesanan_baru_sales',
            'title' => 'Pesanan Tiket Baru (Cash)',
            'message' => 'Pesanan baru #' . str_pad($this->pemesanan->id, 6, '0', STR_PAD_LEFT) . ' dari ' . $this->pemesanan->nama_pemesan . ' menunggu konfirmasi pembayaran Cash.',
            'url' => route('sales.transaksi.index'),
            'icon' => 'banknotes',
            'color' => 'text-emerald-500'
        ];
    }
}
