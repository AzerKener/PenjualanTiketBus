<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_SECRET');
        $this->from = env('TWILIO_WA_FROM');

        try {
            if ($sid && $token) {
                $this->client = new Client($sid, $token);
            }
        } catch (\Exception $e) {
            Log::error('Twilio init error: ' . $e->getMessage());
        }
    }

    /**
     * Send a WhatsApp message
     */
    public function sendWhatsAppMessage($to, $message)
    {
        if (!$this->client) {
            return false;
        }

        $formattedNumber = $this->formatNumber($to);
        if (!$formattedNumber) {
            return false;
        }

        try {
            $this->client->messages->create(
                "whatsapp:" . $formattedNumber,
                [
                    "from" => $this->from,
                    "body" => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format local phone number to international E.164
     */
    private function formatNumber($phone)
    {
        if (!$phone) return null;
        
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        return '+' . $phone;
    }
}
