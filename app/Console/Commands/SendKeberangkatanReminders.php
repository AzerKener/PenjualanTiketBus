<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use Carbon\Carbon;
use App\Notifications\KeberangkatanReminder;
use Illuminate\Support\Facades\Log;

class SendKeberangkatanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat keberangkatan H-1, H-3 jam, dan H-1 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Ambil jadwal yang belum selesai/batal
        $jadwals = Jadwal::with(['supir1.user', 'supir2.user', 'kenek.user', 'penumpangs.pemesanan.user'])
            ->whereIn('status', ['menunggu', 'boarding'])
            ->get();

        foreach ($jadwals as $jadwal) {
            $waktuBerangkat = Carbon::parse(Carbon::parse($jadwal->tanggal_berangkat)->format('Y-m-d') . ' ' . $this->formatWaktu($jadwal->waktu_berangkat));
            $selisihMenit = $now->diffInMinutes($waktuBerangkat, false); // false agar positif jika masa depan

            // Jika jadwal sudah lewat, skip
            if ($selisihMenit < 0) {
                continue;
            }

            // H-1 Hari (24 Jam) -> Antara 23.5 - 24 Jam
            if ($selisihMenit <= 24 * 60 && !$jadwal->notified_h_1) {
                $this->kirimNotifikasi($jadwal, 'H-1');
                $jadwal->update(['notified_h_1' => true]);
            }
            // H-3 Jam -> Antara 2.5 - 3 Jam
            elseif ($selisihMenit <= 3 * 60 && !$jadwal->notified_h_3_hours) {
                $this->kirimNotifikasi($jadwal, 'H-3 Jam');
                $jadwal->update(['notified_h_3_hours' => true]);
            }
            // H-1 Jam -> Antara 0 - 1 Jam
            elseif ($selisihMenit <= 60 && !$jadwal->notified_h_1_hour) {
                $this->kirimNotifikasi($jadwal, 'H-1 Jam');
                $jadwal->update(['notified_h_1_hour' => true]);
            }
        }
    }

    private function formatWaktu($waktu) {
        // Jika hanya H:i, ubah jadi H:i:s
        if (strlen($waktu) == 5) return $waktu . ':00';
        return $waktu;
    }

    private function kirimNotifikasi(Jadwal $jadwal, $jenis)
    {
        // 1. Kirim ke Petugas (Supir/Kenek)
        if ($jadwal->supir1 && $jadwal->supir1->user) {
            $jadwal->supir1->user->notify(new KeberangkatanReminder($jadwal, $jenis, 'supir'));
        }
        if ($jadwal->supir2 && $jadwal->supir2->user) {
            $jadwal->supir2->user->notify(new KeberangkatanReminder($jadwal, $jenis, 'supir'));
        }
        if ($jadwal->kenek && $jadwal->kenek->user) {
            $jadwal->kenek->user->notify(new KeberangkatanReminder($jadwal, $jenis, 'supir'));
        }

        // 2. Kirim ke Penumpang yang pembayarannya sukses (melalui Pemesanan)
        $pemesanans = Pemesanan::where('jadwal_id', $jadwal->id)
            ->where('status_pembayaran', 'lunas')
            ->with('user')
            ->get();
            
        $usersNotified = []; // Mencegah duplikasi jika 1 user pesan banyak tiket
        
        foreach ($pemesanans as $pesanan) {
            if ($pesanan->user && !in_array($pesanan->user_id, $usersNotified)) {
                $pesanan->user->notify(new KeberangkatanReminder($jadwal, $jenis, 'penumpang'));
                $usersNotified[] = $pesanan->user_id;
            }
        }
        
        Log::info("Notifikasi {$jenis} terkirim untuk jadwal ID: {$jadwal->id}");
    }
}
