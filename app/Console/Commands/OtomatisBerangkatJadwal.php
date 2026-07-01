<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Services\TwilioService;

class OtomatisBerangkatJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:berangkat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status jadwal menjadi berangkat sesuai waktunya';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jadwals = Jadwal::with(['supir1.user', 'kenek.user', 'rute'])
            ->whereIn('status', ['menunggu', 'boarding'])
            ->whereDate('tanggal_berangkat', '<=', now()->toDateString())
            ->whereTime('waktu_berangkat', '<=', now()->toTimeString())
            ->get();

        foreach ($jadwals as $jadwal) {
            $jadwal->update(['status' => 'berangkat']);
            
            // Notify Supir via In-App Notification
            if ($jadwal->supir1 && $jadwal->supir1->user) {
                \Illuminate\Support\Facades\Notification::send($jadwal->supir1->user, new \App\Notifications\JadwalBerangkatKru($jadwal));
            }
            
            // Notify Kenek via In-App Notification
            if ($jadwal->kenek && $jadwal->kenek->user) {
                \Illuminate\Support\Facades\Notification::send($jadwal->kenek->user, new \App\Notifications\JadwalBerangkatKru($jadwal));
            }

            $this->info("Jadwal {$jadwal->id} diubah menjadi berangkat.");
        }
    }
}
