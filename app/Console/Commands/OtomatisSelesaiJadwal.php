<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Carbon\Carbon;

class OtomatisSelesaiJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:selesai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status jadwal menjadi selesai saat waktu estimasi tiba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jadwals = Jadwal::whereIn('status', ['berangkat', 'tiba'])->get();

        foreach ($jadwals as $jadwal) {
            if (!$jadwal->estimasi_tiba) continue;

            $berangkatDT = Carbon::parse($jadwal->tanggal_berangkat->format('Y-m-d') . ' ' . $jadwal->waktu_berangkat);
            $tibaDT = Carbon::parse($jadwal->tanggal_berangkat->format('Y-m-d') . ' ' . $jadwal->estimasi_tiba);
            
            // Jika tibaDT lebih kecil dari berangkatDT, berarti beda hari (besoknya)
            if ($tibaDT->lt($berangkatDT)) {
                $tibaDT->addDay();
            }

            if (Carbon::now()->gte($tibaDT)) {
                $jadwal->update(['status' => 'selesai']);
                $this->info("Jadwal {$jadwal->id} otomatis diubah menjadi selesai karena sudah melewati waktu estimasi tiba.");
            }
        }
    }
}
