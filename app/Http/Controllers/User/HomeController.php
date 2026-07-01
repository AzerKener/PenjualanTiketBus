<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Rute;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /** Halaman utama pencarian tiket */
    public function index()
    {
        $asalList   = Rute::orderBy('asal')->pluck('asal')->unique()->values();
        $tujuanList = Rute::orderBy('tujuan')->pluck('tujuan')->unique()->values();
        $rutes      = Rute::orderBy('asal')->get();

        // Jadwal mendatang untuk tampil di beranda
        $jadwalMendatang = Jadwal::with(['bus', 'rute', 'pool'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('tanggal_berangkat', '>=', now()->toDateString())
            ->where('status', 'menunggu')
            ->orderBy('tanggal_berangkat')
            ->limit(6)
            ->get();

        return view('user.home', compact('asalList', 'tujuanList', 'rutes', 'jadwalMendatang'));
    }

    /** Cari jadwal berdasarkan filter */
    public function cari(Request $request)
    {
        $request->validate([
            'asal'             => ['nullable', 'string'],
            'tujuan'           => ['nullable', 'string'],
            'tanggal_berangkat'=> ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $asalList   = Rute::orderBy('asal')->pluck('asal')->unique()->values();
        $tujuanList = Rute::orderBy('tujuan')->pluck('tujuan')->unique()->values();

        $jadwals = Jadwal::with(['bus', 'rute', 'pool', 'penumpangs'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->when($request->filled('asal'), function ($q) use ($request) {
                $q->whereHas('rute', fn ($r) => $r->where('asal', $request->asal));
            })
            ->when($request->filled('tujuan'), function ($q) use ($request) {
                $q->whereHas('rute', fn ($r) => $r->where('tujuan', $request->tujuan));
            })
            ->when($request->filled('tanggal_berangkat'), function ($q) use ($request) {
                // Jika mencari tanggal tertentu, pastikan jamnya belum lewat batas boarding (60 menit)
                $q->whereDate('tanggal_berangkat', $request->tanggal_berangkat)
                  ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [now()->addMinutes(60)]);
            }, function ($q) {
                // Default: tampilkan yang belum lewat batas boarding (60 menit)
                $q->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [now()->addMinutes(60)]);
            })
            ->where('status', 'menunggu')
            ->when($request->filled('tipe_bus'), fn ($q) => $q->whereHas('bus', fn ($b) => $b->where('tipe_bus', $request->tipe_bus)))
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get()
            ->map(function ($j) {
                $j->kursi_terisi   = $j->penumpangs->pluck('nomor_kursi')->toArray();
                $j->kursi_tersedia = $j->bus->jumlah_kursi - count($j->kursi_terisi);
                return $j;
            });

        return view('user.cari', compact('jadwals', 'asalList', 'tujuanList', 'request'));
    }
}
