<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function create(Jadwal $jadwal)
    {
        // Pastikan jadwal ini sudah tiba dan user memang penumpang
        if ($jadwal->status !== 'tiba') {
            return redirect()->route('user.riwayat')->withErrors(['general' => 'Anda hanya bisa memberi ulasan untuk perjalanan yang telah selesai.']);
        }

        $isPenumpang = $jadwal->pemesanans()->where('user_id', Auth::id())->where('status_pembayaran', 'lunas')->exists();
        if (!$isPenumpang) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah sudah memberi rating
        $existing = Rating::where('jadwal_id', $jadwal->id)->where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('user.riwayat')->with('success', 'Anda sudah memberikan ulasan untuk jadwal ini.');
        }

        return view('user.rating.create', compact('jadwal'));
    }

    public function store(Request $request, Jadwal $jadwal)
    {
        if ($jadwal->status !== 'tiba') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:500',
        ]);

        Rating::create([
            'user_id' => Auth::id(),
            'jadwal_id' => $jadwal->id,
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan']
        ]);

        return redirect()->route('user.riwayat')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
