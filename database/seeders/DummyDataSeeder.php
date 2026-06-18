<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Jadwal;
use App\Models\Pool;
use App\Models\Rute;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $pools = Pool::all();
        $rutes = Rute::all();
        $buses = Bus::all();
        $supirs = Pegawai::where('role', 'Supir')->get();
        $keneks = Pegawai::where('role', 'Kenek')->get();

        if ($pools->isEmpty() || $rutes->isEmpty() || $buses->isEmpty()) {
            $this->command->info('Please run standard DatabaseSeeder first to populate base data.');
            return;
        }

        // Generate 10 dummy schedules across today, tomorrow, and the next few days
        for ($i = 0; $i < 10; $i++) {
            $rute = $rutes->random();
            $bus = $buses->random();
            $pool = $pools->random();
            $supir1 = $supirs->random();
            $supir2 = $supirs->count() > 1 ? $supirs->where('id', '!=', $supir1->id)->random() : null;
            $kenek = $keneks->random();
            
            // Random date between today and next 5 days
            $tanggal = Carbon::today()->addDays(rand(0, 5))->format('Y-m-d');
            
            // Random time between 06:00 and 20:00
            $jam = rand(6, 20);
            $waktu = sprintf('%02d:00:00', $jam);
            
            // Base price based on bus type
            $basePrice = 150000;
            if ($bus->tipe_bus === 'VIP') $basePrice = 250000;
            if ($bus->tipe_bus === 'Executive') $basePrice = 350000;

            Jadwal::create([
                'bus_id'           => $bus->id,
                'rute_id'          => $rute->id,
                'pool_id'          => $pool->id,
                'tanggal_berangkat'=> $tanggal,
                'waktu_berangkat'  => $waktu,
                'harga_tiket'      => $basePrice,
                'supir1_id'        => $supir1->id,
                'supir2_id'        => $supir2 ? $supir2->id : null,
                'kenek_id'         => $kenek ? $kenek->id : null,
                'status'           => 'menunggu',
            ]);
        }

        $this->command->info('Successfully added 10 dummy Jadwal (Schedules).');
    }
}
