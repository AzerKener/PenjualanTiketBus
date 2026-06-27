<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = ['nomor_polisi', 'tipe_bus', 'jumlah_kursi', 'fasilitas_bus',];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function getFasilitasAttribute()
    {
        return match ($this->tipe_bus) {

            'Ekonomi' => [
                ['icon'=>'❄️','nama'=>'AC'],
                ['icon'=>'🧳','nama'=>'Bagasi Gratis 20 Kg'],
                ['icon'=>'💧','nama'=>'Air Mineral'],
            ],

            'VIP' => [
                ['icon'=>'❄️','nama'=>'AC'],
                ['icon'=>'📶','nama'=>'WiFi'],
                ['icon'=>'🔌','nama'=>'USB Charger'],
                ['icon'=>'💺','nama'=>'Reclining Seat'],
                ['icon'=>'🧳','nama'=>'Bagasi Gratis 20 Kg'],
                ['icon'=>'💧','nama'=>'Air Mineral'],
                ['icon'=>'🛏️','nama'=>'Selimut'],
            ],

            'Executive' => [
                ['icon'=>'❄️','nama'=>'AC'],
                ['icon'=>'📶','nama'=>'WiFi'],
                ['icon'=>'🔌','nama'=>'USB Charger'],
                ['icon'=>'📺','nama'=>'TV'],
                ['icon'=>'🍱','nama'=>'Snack'],
                ['icon'=>'🧳','nama'=>'Bagasi Gratis 20 Kg'],
                ['icon'=>'🚻','nama'=>'Toilet'],
                ['icon'=>'💺','nama'=>'Reclining Seat'],
                ['icon'=>'🦶','nama'=>'Foot Rest'],
                ['icon'=>'💧','nama'=>'Air Mineral'],
                ['icon'=>'🛏️','nama'=>'Selimut'],
                ['icon'=>'🛌','nama'=>'Bantal'],
            ],
        default => []
    };
}
}
