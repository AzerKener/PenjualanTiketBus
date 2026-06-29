<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'jadwal_id', 'jadwal_pulang_id', 'tipe_pemesanan', 'metode_pembayaran',
        'total_bayar', 'is_round_trip', 'nama_pemesan', 'no_hp_pemesan',
        'tanggal_transaksi', 'user_id', 'sales_id', 'status_pembayaran', 
        'bagasi', 'biaya_bagasi',
    ];

    protected $casts = [
        'is_round_trip'     => 'boolean',
        'total_bayar'       => 'decimal:2',
        'tanggal_transaksi' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function jadwalPulang()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_pulang_id');
    }

    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class);
    }

    public function penumpangsPergi()
    {
        return $this->hasMany(Penumpang::class)->where('jadwal_id', $this->jadwal_id);
    }

    public function penumpangsPulang()
    {
        return $this->hasMany(Penumpang::class)->where('jadwal_id', $this->jadwal_pulang_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }
}
