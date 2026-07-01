<?php
$jadwals = App\Models\Jadwal::all();
foreach($jadwals as $index => $jadwal) {
    $offset = $index % 4;
    if ($offset > 0) {
        $jadwal->update([
            'tanggal_berangkat' => \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->addDays($offset)->toDateString()
        ]);
    }
}
echo 'Done';
