<?php

namespace App\Http\Controllers\Kenek;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Tampilkan jadwal milik kenek yang sedang login.
     */
    public function index()
    {
        // Cari data pegawai berdasarkan nama user yang login
        $pegawai = Pegawai::where('nama', Auth::user()->name)->first();

        if (! $pegawai) {
            return view('kenek.jadwal.index', [
                'jadwals' => collect(),
                'pegawai' => null,
            ])->with('warning', 'Data pegawai tidak ditemukan. Silakan hubungi administrator.');
        }

        $jadwals = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek'])
            ->where('kenek_id', $pegawai->id)
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get();

        return view('kenek.jadwal.index', compact('jadwals', 'pegawai'));
    }

    /**
     * Tampilkan detail satu jadwal milik kenek yang login.
     */
    public function show(int $id)
    {
        $pegawai = Pegawai::where('nama', Auth::user()->name)->firstOrFail();

        $jadwal = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek', 'penumpangs'])
            ->where('kenek_id', $pegawai->id)
            ->findOrFail($id);

        $penumpangs = $jadwal->penumpangs()->orderBy('nomor_kursi')->get();

        return view('kenek.jadwal.show', compact('jadwal', 'pegawai', 'penumpangs'));
    }
}
