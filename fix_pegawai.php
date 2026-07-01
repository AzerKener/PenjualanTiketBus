<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pools = App\Models\Pool::all();
foreach (App\Models\Pegawai::all() as $peg) {
    foreach ($pools as $p) {
        if (strpos($peg->nama, $p->nama_pool) !== false || strpos($peg->nama, str_replace('Pool ', '', $p->nama_pool)) !== false) {
            $peg->pool_id = $p->id;
            $peg->save();
        }
    }
}
echo "Selesai memperbaiki data pool pegawai.\n";
