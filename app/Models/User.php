<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pool_id',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isSales(): bool
    {
        return $this->role === 'Sales';
    }

    public function isSupir(): bool
    {
        return $this->role === 'Supir';
    }

    public function isKenek(): bool
    {
        return $this->role === 'Kenek';
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
