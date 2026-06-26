<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'bus_id', 'rute_id', 'pool_id',
        'tanggal_berangkat', 'waktu_berangkat', 'estimasi_tiba',
        'harga_tiket', 'supir1_id', 'supir2_id', 'kenek_id', 'status',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'harga_tiket'       => 'decimal:2',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function supir1()
    {
        return $this->belongsTo(Pegawai::class, 'supir1_id');
    }

    public function supir2()
    {
        return $this->belongsTo(Pegawai::class, 'supir2_id');
    }

    public function kenek()
    {
        return $this->belongsTo(Pegawai::class, 'kenek_id');
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class);
    }

    /**
     * Kursi yang sudah terisi pada jadwal ini.
     */
    public function kursiTerisi(): array
    {
        return $this->penumpangs()->pluck('nomor_kursi')->toArray();
    }
}
