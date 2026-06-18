<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = ['nomor_polisi', 'tipe_bus', 'jumlah_kursi'];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
