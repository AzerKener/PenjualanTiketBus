<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = ['nama', 'role', 'pool_id', 'no_hp', 'user_id'];

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalSupir1()
    {
        return $this->hasMany(Jadwal::class, 'supir1_id');
    }

    public function jadwalSupir2()
    {
        return $this->hasMany(Jadwal::class, 'supir2_id');
    }

    public function jadwalKenek()
    {
        return $this->hasMany(Jadwal::class, 'kenek_id');
    }

    /**
     * Cek apakah pegawai ini sudah memiliki jadwal pada tanggal tertentu
     * di bus lain dan jadwal tersebut belum berstatus 'selesai'.
     * Digunakan untuk validasi konflik jadwal.
     *
     * @param string $tanggal       Format Y-m-d
     * @param int|null $excludeJadwalId  ID jadwal yang dikecualikan (untuk update)
     * @return bool
     */
    public function hasBentrokanJadwal(string $tanggal, ?int $excludeJadwalId = null): bool
    {
        $query = Jadwal::where('tanggal_berangkat', $tanggal)
            ->where('status', '!=', 'selesai')
            ->where(function ($q) {
                $q->where('supir1_id', $this->id)
                  ->orWhere('supir2_id', $this->id)
                  ->orWhere('kenek_id', $this->id);
            });

        if ($excludeJadwalId) {
            $query->where('id', '!=', $excludeJadwalId);
        }

        return $query->exists();
    }
}
