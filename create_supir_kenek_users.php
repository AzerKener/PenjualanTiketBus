<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$roles = ['Supir', 'Kenek'];
$poolsBogor = ['Pool Baranangsiang', 'Pool Ciawi', 'Pool Cibinong'];

$count = 0;

foreach ($poolsBogor as $poolName) {
    foreach ($roles as $role) {
        for ($i = 1; $i <= 3; $i++) {
            $nama = "{$role} {$i} {$poolName}";
            $pegawai = Pegawai::where('nama', $nama)->first();
            
            if ($pegawai) {
                // Email format: supir1poolbaranangsiang@gmail.com
                $email = strtolower(str_replace(' ', '', $nama)) . '@gmail.com';
                
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => Hash::make('password'),
                        'role' => $role, // 'Supir' or 'Kenek'
                        'pegawai_id' => $pegawai->id
                    ]
                );
                $count++;
            }
        }
    }
}

echo "Berhasil membuat $count akun Supir dan Kenek.\n";
