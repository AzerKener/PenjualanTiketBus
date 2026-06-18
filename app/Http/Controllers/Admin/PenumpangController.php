<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Penumpang;
use Illuminate\Http\Request;

class PenumpangController extends Controller
{
    public function index(Request $request)
    {
        // Jadwals selalu dibutuhkan untuk dropdown form
        $jadwals = Jadwal::with(['bus', 'rute'])
            ->orderByDesc('tanggal_berangkat')
            ->get();

        $jadwal_id = $request->query('jadwal_id');
        $jadwal = null;
        $penumpangs = collect();
        $pesanStatus = null;

        if ($jadwal_id) {
            $jadwal = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek'])
                ->findOrFail($jadwal_id);

            // Cek status bus: hanya tampilkan penumpang jika sudah berangkat atau selesai
            if (!in_array($jadwal->status, ['berangkat', 'selesai'])) {
                $pesanStatus = 'Bus belum berangkat. Daftar penumpang akan tersedia setelah bus berstatus "Berangkat" atau "Selesai".';
            } else {
                $penumpangs = Penumpang::with('pemesanan')
                    ->where('jadwal_id', $jadwal_id)
                    ->orderBy('nomor_kursi')
                    ->get();
            }
        }

        return view('admin.penumpang.index', compact('jadwals', 'jadwal', 'penumpangs', 'pesanStatus'));
    }
}
