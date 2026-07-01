<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Carbon\Carbon;

class OtomatisBoardingJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:boarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status jadwal menjadi boarding 30 menit sebelum berangkat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari jadwal yang statusnya menunggu dan waktunya <= 30 menit dari sekarang
        $targetTime = Carbon::now()->addMinutes(30);
        
        $jadwals = Jadwal::where('status', 'menunggu')
            ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) <= ?", [$targetTime])
            ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [Carbon::now()])
            ->get();

        foreach ($jadwals as $jadwal) {
            $jadwal->update(['status' => 'boarding']);

            // Notify all users in this schedule
            $users = \App\Models\User::whereHas('pemesanans', function ($query) use ($jadwal) {
                $query->where(function ($q) use ($jadwal) {
                    $q->where('jadwal_id', $jadwal->id)
                      ->orWhere('jadwal_pulang_id', $jadwal->id);
                })->whereIn('status_pembayaran', ['lunas', 'selesai']);
            })->get();

            foreach ($users as $user) {
                $user->notify(new \App\Notifications\BusTibaDiPool($jadwal));
            }

            // Notify Supir & Kenek
            if ($jadwal->supir1 && $jadwal->supir1->user) {
                $jadwal->supir1->user->notify(new \App\Notifications\BusTibaDiPool($jadwal));
            }
            if ($jadwal->supir2 && $jadwal->supir2->user) {
                $jadwal->supir2->user->notify(new \App\Notifications\BusTibaDiPool($jadwal));
            }
            if ($jadwal->kenek && $jadwal->kenek->user) {
                $jadwal->kenek->user->notify(new \App\Notifications\BusTibaDiPool($jadwal));
            }

            $this->info("Jadwal {$jadwal->id} diubah menjadi boarding (30 menit sebelum berangkat).");
        }
    }
}
