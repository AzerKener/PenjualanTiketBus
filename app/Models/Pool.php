<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    protected $fillable = ['nama_pool', 'lokasi'];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
