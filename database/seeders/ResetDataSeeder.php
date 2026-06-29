<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pool;
use App\Models\Rute;
use App\Models\Jadwal;
use App\Models\Bus;
use App\Models\Pegawai;
use Carbon\Carbon;

class ResetDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Disable Foreign Key Checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Truncate Tables
        DB::table('ratings')->truncate();
        DB::table('penumpangs')->truncate();
        DB::table('pemesanans')->truncate();
        DB::table('jadwals')->truncate();
        DB::table('rutes')->truncate();
        DB::table('pools')->truncate();

        // 3. Enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Create Realistic Pools
        $poolsData = [
            ['nama_pool' => 'Pool Kampung Rambutan', 'lokasi' => 'Jakarta Timur', 'latitude' => '-6.3087', 'longitude' => '106.8833'],
            ['nama_pool' => 'Pool Pulo Gebang', 'lokasi' => 'Jakarta Timur', 'latitude' => '-6.2088', 'longitude' => '106.9492'],
            ['nama_pool' => 'Pool Leuwi Panjang', 'lokasi' => 'Bandung', 'latitude' => '-6.9463', 'longitude' => '107.5947'],
            ['nama_pool' => 'Pool Giwangan', 'lokasi' => 'Yogyakarta', 'latitude' => '-7.8286', 'longitude' => '110.3958'],
            ['nama_pool' => 'Pool Tirtonadi', 'lokasi' => 'Surakarta', 'latitude' => '-7.5552', 'longitude' => '110.8228'],
            ['nama_pool' => 'Pool Bungurasih', 'lokasi' => 'Surabaya', 'latitude' => '-7.3524', 'longitude' => '112.7214'],
        ];

        $pools = [];
        foreach ($poolsData as $p) {
            $pools[$p['nama_pool']] = Pool::create($p);
        }

        // 5. Create Realistic Rutes (Pasangan Pulang Pergi)
        $rutesData = [
            ['asal' => 'Jakarta', 'tujuan' => 'Bandung'],
            ['asal' => 'Bandung', 'tujuan' => 'Jakarta'], // Balikan
            
            ['asal' => 'Jakarta', 'tujuan' => 'Yogyakarta'],
            ['asal' => 'Yogyakarta', 'tujuan' => 'Jakarta'], // Balikan
            
            ['asal' => 'Bandung', 'tujuan' => 'Yogyakarta'],
            ['asal' => 'Yogyakarta', 'tujuan' => 'Bandung'], // Balikan
            
            ['asal' => 'Yogyakarta', 'tujuan' => 'Surabaya'],
            ['asal' => 'Surabaya', 'tujuan' => 'Yogyakarta'], // Balikan
            
            ['asal' => 'Surakarta', 'tujuan' => 'Jakarta'],
            ['asal' => 'Jakarta', 'tujuan' => 'Surakarta'], // Balikan
            
            ['asal' => 'Surabaya', 'tujuan' => 'Jakarta'],
            ['asal' => 'Jakarta', 'tujuan' => 'Surabaya'], // Balikan
        ];

        $rutes = [];
        foreach ($rutesData as $r) {
            $rutes[] = Rute::create($r);
        }

        // 6. Create Jadwals for the next 7 days
        $buses = Bus::all();
        
        // Reassign Pegawais to new pools first so we can use them as Supir
        $allPoolIds = array_map(function($p) { return $p->id; }, array_values($pools));
        if (count($allPoolIds) > 0) {
            $pegawais = Pegawai::all();
            foreach ($pegawais as $pegawai) {
                $pegawai->pool_id = $allPoolIds[array_rand($allPoolIds)];
                $pegawai->save();
            }
        }
        
        $supirs = Pegawai::where('role', 'Supir')->get();

        if ($buses->count() > 0 && $supirs->count() > 0) {
            $now = Carbon::now();
            $poolValues = array_values($pools);

            // Buat 10 Pasang Jadwal (Pergi dan Pulang)
            for ($i = 0; $i < 10; $i++) {
                // Pilih rute Pergi secara acak (pilih yang indexnya genap agar selalu jadi asal)
                $rutePergi = $rutes[rand(0, 5) * 2];
                // Rute pulang otomatis yang indexnya ganjil (setelahnya)
                $rutePulang = Rute::where('asal', $rutePergi->tujuan)->where('tujuan', $rutePergi->asal)->first();
                
                $bus = $buses->random();
                $supir = $supirs->random();
                
                // Helper function untuk menentukan pool
                $getPool = function($kota) use ($pools, $poolValues) {
                    if ($kota == 'Jakarta') return (rand(0, 1) == 0) ? $pools['Pool Kampung Rambutan'] : $pools['Pool Pulo Gebang'];
                    if ($kota == 'Bandung') return $pools['Pool Leuwi Panjang'];
                    if ($kota == 'Yogyakarta') return $pools['Pool Giwangan'];
                    if ($kota == 'Surakarta') return $pools['Pool Tirtonadi'];
                    if ($kota == 'Surabaya') return $pools['Pool Bungurasih'];
                    return $poolValues[array_rand($poolValues)];
                };

                $poolAsalPergi = $getPool($rutePergi->asal);
                $poolTujuanPergi = $getPool($rutePergi->tujuan);

                $tanggalBerangkat = $now->copy()->addDays(rand(1, 5));
                $waktuBerangkatHour = rand(6, 12); // Pagi hari
                $waktuBerangkat = sprintf('%02d:00:00', $waktuBerangkatHour); 
                $estimasiTiba = sprintf('%02d:00:00', ($waktuBerangkatHour + rand(3, 10)) % 24); 

                $harga = match($rutePergi->asal) {
                    'Jakarta' => match($rutePergi->tujuan) { 'Bandung' => 120000, 'Yogyakarta' => 250000, 'Surakarta' => 260000, 'Surabaya' => 350000, default => 200000 },
                    'Bandung' => match($rutePergi->tujuan) { 'Yogyakarta' => 220000, default => 200000 },
                    'Yogyakarta' => match($rutePergi->tujuan) { 'Surabaya' => 180000, default => 200000 },
                    'Surakarta' => 260000,
                    'Surabaya' => 350000,
                    default => 250000,
                };

                // 1. Buat Jadwal Pergi
                Jadwal::create([
                    'rute_id' => $rutePergi->id,
                    'bus_id' => $bus->id,
                    'pool_id' => $poolAsalPergi->id,
                    'pool_tujuan_id' => $poolTujuanPergi->id,
                    'tanggal_berangkat' => $tanggalBerangkat->toDateString(),
                    'waktu_berangkat' => $waktuBerangkat,
                    'estimasi_tiba' => $estimasiTiba,
                    'harga_tiket' => $harga,
                    'supir1_id' => $supir->id,
                    'status' => 'menunggu'
                ]);

                // 2. Buat Jadwal Pulang (Balikan keesokan harinya menggunakan bus & supir yang sama)
                // Waktu berangkatnya misal sore hari
                $tanggalPulang = $tanggalBerangkat->copy()->addDays(rand(1, 3));
                $waktuPulangHour = rand(14, 20); // Sore/Malam
                $waktuPulang = sprintf('%02d:00:00', $waktuPulangHour); 
                $estimasiTibaPulang = sprintf('%02d:00:00', ($waktuPulangHour + rand(3, 10)) % 24); 

                Jadwal::create([
                    'rute_id' => $rutePulang->id,
                    'bus_id' => $bus->id, // Asumsi bus yang sama balik lagi
                    'pool_id' => $poolTujuanPergi->id, // Berangkat dari pool tujuan pergi
                    'pool_tujuan_id' => $poolAsalPergi->id, // Pulang ke pool asal pergi
                    'tanggal_berangkat' => $tanggalPulang->toDateString(),
                    'waktu_berangkat' => $waktuPulang,
                    'estimasi_tiba' => $estimasiTibaPulang,
                    'harga_tiket' => $harga, // Harga sama
                    'supir1_id' => $supir->id,
                    'status' => 'menunggu'
                ]);
            }
        }

        echo "Reset Data Completed: Truncated tables, added realistic Pools, Routes, Schedules, and reassigned Pegawais.\n";
    }
}
