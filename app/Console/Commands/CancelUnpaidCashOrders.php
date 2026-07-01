<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pemesanan;
use Carbon\Carbon;

class CancelUnpaidCashOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pemesanan:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan semua pesanan yang sudah lebih dari 8 jam belum dikonfirmasi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitTime = Carbon::now()->subHours(8);

        $expiredPemesanans = Pemesanan::where('status_pembayaran', 'pending')
            ->where('created_at', '<', $limitTime)
            ->get();

        foreach ($expiredPemesanans as $pemesanan) {
            $pemesanan->update(['status_pembayaran' => 'batal']);
            
            if ($pemesanan->user) {
                $pemesanan->user->notify(new \App\Notifications\UpdateStatusTiket($pemesanan, 'dibatalkan', 'Melebihi batas waktu pembayaran 8 jam.'));
            }

            // Delete associated penumpangs so seats are freed
            $pemesanan->penumpangs()->delete();

            $this->info("Pesanan #{$pemesanan->id} dibatalkan karena melebihi 8 jam.");
        }
    }
}
