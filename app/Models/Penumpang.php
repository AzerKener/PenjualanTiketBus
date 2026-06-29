<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penumpang extends Model
{
    protected $fillable = ['pemesanan_id', 'jadwal_id', 'nomor_kursi', 'nama_penumpang', 'bagasi_kg',
    'biaya_bagasi'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
