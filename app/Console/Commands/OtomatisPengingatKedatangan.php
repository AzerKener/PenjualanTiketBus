<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Carbon\Carbon;

class OtomatisPengingatKedatangan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:pengingat-kedatangan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat kedatangan 1 jam sebelum estimasi tiba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetTime = Carbon::now()->addMinutes(60)->format('H:i');
        
        $jadwals = Jadwal::where('status', 'berangkat')
            ->get();

        foreach ($jadwals as $jadwal) {
            // Because estimasi_tiba is stored as 'H:i:s' or 'H:i', we parse it to H:i
            $estimasi = Carbon::parse($jadwal->estimasi_tiba)->format('H:i');
            
            if ($estimasi === $targetTime) {
                $users = \App\Models\User::whereHas('pemesanans', function ($query) use ($jadwal) {
                    $query->where(function ($q) use ($jadwal) {
                        $q->where('jadwal_id', $jadwal->id)
                          ->orWhere('jadwal_pulang_id', $jadwal->id);
                    })->whereIn('status_pembayaran', ['lunas', 'selesai']);
                })->get();

                foreach ($users as $user) {
                    $user->notify(new \App\Notifications\KedatanganReminder($jadwal, $jadwal->estimasi_tiba));
                }

                $this->info("Pengingat kedatangan dikirim untuk jadwal {$jadwal->id}.");
            }
        }
    }
}
