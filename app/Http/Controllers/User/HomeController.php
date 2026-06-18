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
            'asal'             => ['required', 'string'],
            'tujuan'           => ['required', 'string'],
            'tanggal_berangkat'=> ['required', 'date', 'after_or_equal:today'],
        ], [
            'asal.required'              => 'Kota asal wajib dipilih.',
            'tujuan.required'            => 'Kota tujuan wajib dipilih.',
            'tanggal_berangkat.required' => 'Tanggal keberangkatan wajib diisi.',
            'tanggal_berangkat.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ]);

        $asalList   = Rute::orderBy('asal')->pluck('asal')->unique()->values();
        $tujuanList = Rute::orderBy('tujuan')->pluck('tujuan')->unique()->values();

        $jadwals = Jadwal::with(['bus', 'rute', 'pool', 'penumpangs'])
            ->whereHas('rute', function ($q) use ($request) {
                $q->where('asal', $request->asal)
                  ->where('tujuan', $request->tujuan);
            })
            ->whereDate('tanggal_berangkat', $request->tanggal_berangkat)
            ->where('status', 'menunggu')
            ->when($request->filled('tipe_bus'), fn ($q) => $q->whereHas('bus', fn ($b) => $b->where('tipe_bus', $request->tipe_bus)))
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
