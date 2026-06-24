<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Jadwal;
use App\Models\Pegawai;
use App\Models\Pool;
use App\Models\Rute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Pools ───────────────────────────────────────────────
        $pool1 = Pool::create([
            'nama_pool' => 'Pool Utara',
            'lokasi'    => 'Jl. Pasar Minggu No. 12, Jakarta Selatan',
        ]);

        $pool2 = Pool::create([
            'nama_pool' => 'Pool Timur',
            'lokasi'    => 'Jl. Bekasi Raya No. 45, Bekasi',
        ]);

        $pool3 = Pool::create([
            'nama_pool' => 'Pool Barat',
            'lokasi'    => 'Jl. Daan Mogot No. 88, Tangerang',
        ]);

        // ─── Users ───────────────────────────────────────────────
        User::create([
            'name'     => 'Admin Utama',
            'email'    => 'admin@busticket.id',
            'password' => Hash::make('password123'),
            'role'     => 'Admin',
            'pool_id'  => $pool1->id,
            'no_hp'    => '081200000001',
        ]);

        User::create([
            'name'     => 'Sales Pool Utara',
            'email'    => 'sales@busticket.id',
            'password' => Hash::make('password123'),
            'role'     => 'Sales',
            'pool_id'  => $pool1->id,
            'no_hp'    => '081200000002',
        ]);

        User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'supir@busticket.id',
            'password' => Hash::make('password123'),
            'role'     => 'Supir',
            'pool_id'  => $pool1->id,
            'no_hp'    => '081200000003',
        ]);

        // ─── Pegawai ─────────────────────────────────────────────
        $supir1 = Pegawai::create([
            'nama' => 'Budi Santoso',
            'role' => 'Supir',
            'pool_id' => $pool1->id,
            'no_hp' => '081234567890'
        ]);

        $supir2 = Pegawai::create([
            'nama' => 'Ahmad Fauzi',
            'role' => 'Supir',
            'pool_id' => $pool1->id,
            'no_hp' => '081298765432'
        ]);

        $supir3 = Pegawai::create([
            'nama' => 'Rizky Pratama',
            'role' => 'Supir',
            'pool_id' => $pool2->id,
            'no_hp' => '082112345678'
        ]);

        $kenek1 = Pegawai::create([
            'nama' => 'Slamet Riyadi',
            'role' => 'Kenek',
            'pool_id' => $pool1->id,
            'no_hp' => '085611112222'
        ]);

        $kenek2 = Pegawai::create([
            'nama' => 'Dian Permana',
            'role' => 'Kenek',
            'pool_id' => $pool2->id,
            'no_hp' => '085699988877'
        ]);

        // ─── Rute ────────────────────────────────────────────────
        $rute1 = Rute::create(['asal' => 'Jakarta', 'tujuan' => 'Surabaya']);
        $rute2 = Rute::create(['asal' => 'Surabaya', 'tujuan' => 'Jakarta']);
        $rute3 = Rute::create(['asal' => 'Jakarta', 'tujuan' => 'Yogyakarta']);
        $rute4 = Rute::create(['asal' => 'Yogyakarta', 'tujuan' => 'Jakarta']);
        $rute5 = Rute::create(['asal' => 'Jakarta', 'tujuan' => 'Bandung']);
        $rute6 = Rute::create(['asal' => 'Bandung', 'tujuan' => 'Jakarta']);

        // ─── Bus ────────────────────────────────────────────────
        $bus1 = Bus::create(['nomor_polisi' => 'B 1234 AB', 'tipe_bus' => 'Ekonomi', 'jumlah_kursi' => 40]);
        $bus2 = Bus::create(['nomor_polisi' => 'B 5678 CD', 'tipe_bus' => 'VIP', 'jumlah_kursi' => 32]);
        $bus3 = Bus::create(['nomor_polisi' => 'B 9999 EF', 'tipe_bus' => 'Executive', 'jumlah_kursi' => 24]);
        $bus4 = Bus::create(['nomor_polisi' => 'D 1111 GH', 'tipe_bus' => 'Ekonomi', 'jumlah_kursi' => 40]);

        // ─── Jadwal ──────────────────────────────────────────────
        $tanggalBesok  = now()->addDay()->format('Y-m-d');
        $tanggalLusa   = now()->addDays(2)->format('Y-m-d');
        $tanggalMinggu = now()->addDays(7)->format('Y-m-d');

        Jadwal::create([
            'bus_id' => $bus1->id,
            'rute_id' => $rute1->id,
            'pool_id' => $pool1->id,
            'tanggal_berangkat' => $tanggalBesok,
            'waktu_berangkat' => '08:00:00',
            'harga_tiket' => 250000,
            'supir1_id' => $supir1->id,
            'supir2_id' => $supir2->id,
            'kenek_id' => $kenek1->id,
            'status' => 'menunggu',
        ]);

        Jadwal::create([
            'bus_id' => $bus2->id,
            'rute_id' => $rute1->id,
            'pool_id' => $pool2->id,
            'tanggal_berangkat' => $tanggalBesok,
            'waktu_berangkat' => '14:00:00',
            'harga_tiket' => 380000,
            'supir1_id' => $supir3->id,
            'kenek_id' => $kenek2->id,
            'status' => 'menunggu',
        ]);

        Jadwal::create([
            'bus_id' => $bus3->id,
            'rute_id' => $rute3->id,
            'pool_id' => $pool1->id,
            'tanggal_berangkat' => $tanggalLusa,
            'waktu_berangkat' => '07:00:00',
            'harga_tiket' => 450000,
            'supir1_id' => $supir2->id,
            'kenek_id' => $kenek1->id,
            'status' => 'menunggu',
        ]);

        Jadwal::create([
            'bus_id' => $bus1->id,
            'rute_id' => $rute5->id,
            'pool_id' => $pool3->id,
            'tanggal_berangkat' => $tanggalMinggu,
            'waktu_berangkat' => '06:30:00',
            'harga_tiket' => 150000,
            'supir1_id' => $supir1->id,
            'kenek_id' => $kenek1->id,
            'status' => 'menunggu',
        ]);
    }
}