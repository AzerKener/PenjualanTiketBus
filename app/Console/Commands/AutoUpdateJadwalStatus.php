<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use Carbon\Carbon;

class AutoUpdateJadwalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek jadwal dan memberikan keterangan telat jika lewat 60 menit belum berangkat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Cari semua jadwal yang masih "menunggu" hari ini atau sebelumnya
        $jadwals = Jadwal::where('status', 'menunggu')
            ->whereDate('tanggal_berangkat', '<=', $now->toDateString())
            ->get();

        foreach ($jadwals as $jadwal) {
            $jadwalWaktu = Carbon::parse($jadwal->tanggal_berangkat . ' ' . $jadwal->waktu_berangkat);
            
            // Jika jadwal sudah lewat dari 60 menit
            if ($now->diffInMinutes($jadwalWaktu, false) <= -60) {
                // Update keterangan (hanya jika belum diset untuk menghindari update berulang kali)
                if (empty($jadwal->keterangan)) {
                    $jadwal->keterangan = 'Telat Berangkat: Supir belum konfirmasi keberangkatan setelah 60 menit dari jadwal.';
                    $jadwal->save();
                    
                    $this->info("Jadwal ID {$jadwal->id} ditandai telat.");
                }
            }
        }
        
        $this->info('Pengecekan jadwal selesai.');
    }
}
