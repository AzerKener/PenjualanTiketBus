<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'Sales')->where('name', 'LIKE', 'Sales %Pool Baranangsiang')->first();
echo "Pool ID: " . $user->pool_id . "\n";
echo "Pegawai ID: " . $user->pegawai_id . "\n";
$peg = App\Models\Pegawai::find($user->pegawai_id);
if ($peg) echo "Pegawai Pool ID: " . $peg->pool_id . "\n";
