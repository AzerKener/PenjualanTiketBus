<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\User;
use App\Notifications\UpdateStatusTiket;
use Illuminate\Support\Facades\Notification;

class TwilioController extends Controller
{
    public function handle(Request $request)
    {
        // Twilio sends data in application/x-www-form-urlencoded
        // $request->input('From')  => e.g. "whatsapp:+62812xxx"
        // $request->input('Body')  => e.g. "Bayar"
        
        $from = $request->input('From');
        $body = trim($request->input('Body'));

        if (!$from || !$body) {
            return response('No data', 400);
        }

        // Format number: remove "whatsapp:" prefix and "+" if necessary, to match DB.
        // Usually Twilio 'From' is like 'whatsapp:+6281296374250'.
        // Our DB saves numbers like '081296374250' or '6281296374250' depending on how user entered it.
        // Let's just find a matching Pemesanan by comparing rightmost digits, or do a loose match.
        // Alternatively, if body is exactly "Bayar" or "bayar"
        
        if (strtolower($body) === 'bayar') {
            // Parse phone number loosely. e.g., '6281296374250' -> '081296374250'
            $rawPhone = str_replace(['whatsapp:', '+', '-', ' '], '', $from);
            
            // Generate possible variants to match DB (e.g. 0812... or 62812...)
            $phoneVariant1 = $rawPhone; 
            $phoneVariant2 = preg_replace('/^62/', '0', $rawPhone);
            $phoneVariant3 = preg_replace('/^0/', '62', $rawPhone);

            // Find pending pemesanan for this phone number
            $pemesanan = Pemesanan::whereIn('no_hp_pemesan', [$phoneVariant1, $phoneVariant2, $phoneVariant3])
                ->where('status_pembayaran', 'pending')
                ->latest()
                ->first();

            if ($pemesanan) {
                // Mark as lunas
                $pemesanan->update(['status_pembayaran' => 'lunas']);

                // Notify User
                if ($pemesanan->user) {
                    $pemesanan->user->notify(new UpdateStatusTiket($pemesanan, 'lunas'));
                }

                // Notify Admin (Dana Masuk)
                $adminUsers = User::where('role', 'Admin')->get();
                // Send a generic notification to admin that payment is received
                Notification::send($adminUsers, new UpdateStatusTiket($pemesanan, 'lunas'));

                // Reply via Twilio (TwiML)
                $reply = "Terima kasih, pembayaran tiket #" . str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) . " Anda telah BERHASIL kami verifikasi (SIMULASI TWILIO).\n\n";
                $reply .= "Berikut adalah tautan E-Tiket Anda:\n";
                $reply .= route('user.etiket', $pemesanan->id) . "\n\n";
                $reply .= "Silakan tunjukkan E-Tiket ini kepada petugas saat keberangkatan.";

                return response("<Response><Message><Body>" . htmlspecialchars($reply) . "</Body></Message></Response>")
                    ->header('Content-Type', 'text/xml');
            } else {
                $reply = "Maaf, kami tidak menemukan pesanan tiket yang sedang pending untuk nomor Anda.";
                return response("<Response><Message><Body>" . htmlspecialchars($reply) . "</Body></Message></Response>")
                    ->header('Content-Type', 'text/xml');
            }
        }

        // Just empty response for other messages
        return response("<Response></Response>")->header('Content-Type', 'text/xml');
    }
}
