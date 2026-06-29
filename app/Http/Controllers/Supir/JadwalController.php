<?php

namespace App\Http\Controllers\Supir;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Tampilkan jadwal milik supir yang sedang login.
     *
     * Pencocokan dilakukan dengan mencari Pegawai yang nama-nya sama
     * dengan nama User yang login (auth()->user()->name).
     * Jadwal ditampilkan jika supir1_id atau supir2_id cocok dengan id pegawai.
     */
    public function index()
    {
        // Cari data pegawai berdasarkan nama user yang login
        $pegawai = Pegawai::where('nama', Auth::user()->name)->first();

        if (! $pegawai) {
            // Jika data pegawai tidak ditemukan, kembalikan view dengan pesan
            return view('supir.jadwal.index', [
                'jadwals' => collect(),
                'pegawai' => null,
            ])->with('warning', 'Data pegawai tidak ditemukan. Silakan hubungi administrator.');
        }

        $jadwals = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek'])
            ->where(function ($q) use ($pegawai) {
                $q->where('supir1_id', $pegawai->id)
                  ->orWhere('supir2_id', $pegawai->id);
            })
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get();

        return view('supir.jadwal.index', compact('jadwals', 'pegawai'));
    }

    /**
     * Tampilkan detail satu jadwal milik supir yang login.
     */
    public function show(int $id)
    {
        $pegawai = Pegawai::where('nama', Auth::user()->name)->firstOrFail();

        $jadwal = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek', 'penumpangs'])
            ->where(function ($q) use ($pegawai) {
                $q->where('supir1_id', $pegawai->id)
                  ->orWhere('supir2_id', $pegawai->id);
            })
            ->findOrFail($id);

        $penumpangs = $jadwal->penumpangs()->orderBy('nomor_kursi')->get();

        return view('supir.jadwal.show', compact('jadwal', 'pegawai', 'penumpangs'));
    }

    /**
     * Update status jadwal (menunggu -> boarding -> berangkat -> tiba)
     */
    public function updateStatus(Request $request, int $id)
    {
        $pegawai = Pegawai::where('nama', Auth::user()->name)->firstOrFail();

        $jadwal = Jadwal::where(function ($q) use ($pegawai) {
                $q->where('supir1_id', $pegawai->id)
                  ->orWhere('supir2_id', $pegawai->id);
            })
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:menunggu,boarding,berangkat,tiba,selesai'
        ]);

        $oldStatus = $jadwal->status;
        $jadwal->update(['status' => $request->status]);

        if ($request->status === 'selesai' && $oldStatus !== 'selesai') {
            // 1. Kirim ucapan terima kasih ke penumpang
            $pemesanans = \App\Models\Pemesanan::where('jadwal_id', $jadwal->id)
                ->where('status_pembayaran', 'lunas')
                ->with('user')
                ->get();
            
            $usersNotified = [];
            foreach ($pemesanans as $pesanan) {
                if ($pesanan->user && !in_array($pesanan->user_id, $usersNotified)) {
                    $pesanan->user->notify(new \App\Notifications\PerjalananSelesai($jadwal));
                    $usersNotified[] = $pesanan->user_id;
                }
            }

            // 2. Kirim jadwal selanjutnya ke petugas (Supir1, Supir2, Kenek)
            $petugas = [$jadwal->supir1, $jadwal->supir2, $jadwal->kenek];
            foreach ($petugas as $p) {
                if ($p && $p->user) {
                    $nextJadwal = \App\Models\Jadwal::where(function($q) use ($p) {
                            $q->where('supir1_id', $p->id)
                              ->orWhere('supir2_id', $p->id)
                              ->orWhere('kenek_id', $p->id);
                        })
                        ->where('status', 'menunggu')
                        ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [\Carbon\Carbon::now()])
                        ->orderByRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) ASC")
                        ->first();
                        
                    if ($nextJadwal) {
                        $p->user->notify(new \App\Notifications\JadwalSelanjutnya($nextJadwal));
                    }
                }
            }
        }

        return back()->with('success', 'Status jadwal berhasil diperbarui menjadi: ' . ucfirst($request->status));
    }
}
