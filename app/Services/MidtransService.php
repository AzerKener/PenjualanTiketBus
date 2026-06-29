<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('services.midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = config('services.midtrans.is_production');
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
    }

    public function createSnapToken($pemesanan)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $pemesanan->id . '-' . time(), // Order ID must be unique
                'gross_amount' => $pemesanan->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $pemesanan->nama_pemesan,
                'phone' => $pemesanan->no_hp_pemesan,
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            return null;
        }
    }
}
