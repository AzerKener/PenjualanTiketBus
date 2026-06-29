<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Pool;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersAndPegawaiSeeder extends Seeder
{
    public function run()
    {
        $pools = Pool::all();
        if ($pools->isEmpty()) {
            $this->command->info('Tidak ada pool. Harap jalankan ResetDataSeeder terlebih dahulu.');
            return;
        }

        $roles = ['Supir', 'Kenek', 'Sales'];
        
        $outputFile = storage_path('app/public/user_credentials.txt');
        $credentials = [];
        $credentials[] = str_pad("NAMA", 25) . " | " . str_pad("ROLE", 10) . " | " . str_pad("POOL", 25) . " | " . str_pad("EMAIL", 30) . " | PASSWORD";
        $credentials[] = str_repeat("-", 110);

        foreach ($pools as $pool) {
            foreach ($roles as $role) {
                // Buat 3 orang untuk setiap role per pool
                for ($i = 1; $i <= 3; $i++) {
                    $nama = $role . ' ' . $i . ' ' . $pool->nama_pool;
                    $email = strtolower($role) . $i . '_' . Str::slug($pool->nama_pool) . '@tiketbus.com';
                    $passwordText = 'password123'; // Password sama untuk semua agar mudah diingat

                    // Buat User
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'name' => $nama,
                            'password' => Hash::make($passwordText),
                            'role' => $role,
                        ]
                    );

                    // Buat Pegawai
                    Pegawai::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama' => $nama,
                            'role' => $role,
                            'pool_id' => $pool->id,
                            'no_hp' => '08' . rand(1000000000, 9999999999),
                        ]
                    );

                    $credentials[] = str_pad($nama, 25) . " | " . str_pad($role, 10) . " | " . str_pad($pool->nama_pool, 25) . " | " . str_pad($email, 30) . " | " . $passwordText;
                }
            }
        }
        
        // Simpan ke file txt agar bisa dibaca
        file_put_contents($outputFile, implode("\n", $credentials));
        $this->command->info('Berhasil membuat banyak user. Kredensial disimpan di: ' . $outputFile);
    }
}
