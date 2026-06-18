<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    protected $fillable = ['asal', 'tujuan'];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->asal . ' → ' . $this->tujuan;
    }
}
