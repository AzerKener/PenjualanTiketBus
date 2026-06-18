<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use App\Models\Rute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    /**
     * Halaman utama / form pencarian rute.
     */
    public function index()
    {
        $rutes = Rute::orderBy('asal')->get();

        $asalList   = Rute::select('asal')->distinct()->orderBy('asal')->pluck('asal');
        $tujuanList = Rute::select('tujuan')->distinct()->orderBy('tujuan')->pluck('tujuan');
        $tipesBus   = ['Ekonomi', 'Bisnis', 'Eksekutif'];

        return view('sales.pemesanan.index', compact('rutes', 'asalList', 'tujuanList', 'tipesBus'));
    }

    /**
     * Cari jadwal berdasarkan asal, tujuan, tanggal_berangkat, tipe_bus.
     * Mendukung AJAX (return JSON) maupun request biasa (return view).
     */
    public function cariJadwal(Request $request)
    {
        $request->validate([
            'asal'              => 'required|string|max:100',
            'tujuan'            => 'required|string|max:100',
            'tanggal_berangkat' => 'required|date|after_or_equal:today',
            'tipe_bus'          => 'nullable|string|in:Ekonomi,Bisnis,Eksekutif',
        ]);

        $query = Jadwal::with(['bus', 'rute', 'pool', 'penumpangs'])
            ->whereHas('rute', function ($q) use ($request) {
                $q->where('asal', $request->asal)
                  ->where('tujuan', $request->tujuan);
            })
            ->whereDate('tanggal_berangkat', $request->tanggal_berangkat)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan');

        if ($request->filled('tipe_bus')) {
            $query->whereHas('bus', function ($q) use ($request) {
                $q->where('tipe_bus', $request->tipe_bus);
            });
        }

        $jadwals = $query->orderBy('waktu_berangkat')->get();

        // Tambahkan info kursi tersedia untuk setiap jadwal
        $jadwals->each(function ($jadwal) {
            $jadwal->kursi_terisi   = $jadwal->kursiTerisi();
            $jadwal->kursi_tersedia = $jadwal->bus->jumlah_kursi - count($jadwal->kursi_terisi);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $jadwals,
            ]);
        }

        $asalList   = Rute::select('asal')->distinct()->orderBy('asal')->pluck('asal');
        $tujuanList = Rute::select('tujuan')->distinct()->orderBy('tujuan')->pluck('tujuan');
        $tipesBus   = ['Ekonomi', 'Bisnis', 'Eksekutif'];

        return view('sales.pemesanan.index', compact(
            'jadwals',
            'asalList',
            'tujuanList',
            'tipesBus'
        ))->with('search', $request->only('asal', 'tujuan', 'tanggal_berangkat', 'tipe_bus'));
    }

    /**
     * Tampilkan detail jadwal beserta denah kursi.
     * Kursi terisi diambil dari tabel Penumpang berdasarkan jadwal_id.
     */
    public function pilihJadwal(int $jadwalId)
    {
        $jadwal = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek', 'penumpangs'])
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->findOrFail($jadwalId);

        $kursiTerisi  = $jadwal->kursiTerisi();
        $jumlahKursi  = $jadwal->bus->jumlah_kursi;

        // Bangun array kursi: 1 s/d jumlah_kursi
        $semuaKursi = range(1, $jumlahKursi);

        // Rute pulang yang tersedia (asal-tujuan terbalik) untuk pilihan round trip
        $jadwalPulangList = Jadwal::with(['bus', 'rute'])
            ->whereHas('rute', function ($q) use ($jadwal) {
                $q->where('asal', $jadwal->rute->tujuan)
                  ->where('tujuan', $jadwal->rute->asal);
            })
            ->whereDate('tanggal_berangkat', '>=', $jadwal->tanggal_berangkat)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get();

        return view('sales.pemesanan.pilih-jadwal', compact(
            'jadwal',
            'kursiTerisi',
            'semuaKursi',
            'jadwalPulangList'
        ));
    }

    /**
     * Proses pemesanan tiket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id'          => 'required|exists:jadwals,id',
            'nama_pemesan'       => 'required|string|max:100',
            'no_hp_pemesan'      => 'required|string|max:20',
            'metode_pembayaran'  => 'required|in:Cash,Transfer,E-Wallet',
            'kursi'              => 'required|array|min:1',
            'kursi.*'            => 'required|integer|min:1',
            'nama_penumpang'     => 'required|array|min:1',
            'nama_penumpang.*'   => 'required|string|max:100',
            'is_round_trip'      => 'nullable|boolean',
            'jadwal_pulang_id'   => 'nullable|required_if:is_round_trip,1|exists:jadwals,id',
        ], [
            'jadwal_id.required'         => 'Jadwal keberangkatan wajib dipilih.',
            'nama_pemesan.required'      => 'Nama pemesan wajib diisi.',
            'no_hp_pemesan.required'     => 'Nomor HP pemesan wajib diisi.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'       => 'Metode pembayaran tidak valid.',
            'kursi.required'             => 'Pilih minimal satu kursi.',
            'nama_penumpang.required'    => 'Nama penumpang wajib diisi.',
            'jadwal_pulang_id.required_if' => 'Jadwal pulang wajib dipilih untuk perjalanan pulang-pergi.',
        ]);

        // Pastikan jumlah kursi == jumlah nama penumpang
        if (count($validated['kursi']) !== count($validated['nama_penumpang'])) {
            return back()
                ->withInput()
                ->withErrors(['kursi' => 'Jumlah kursi dan nama penumpang harus sama.']);
        }

        $jadwal = Jadwal::with('bus')->findOrFail($validated['jadwal_id']);

        // Cek apakah kursi masih tersedia
        $kursiTerisiPergi = $jadwal->kursiTerisi();
        $kursiDipesan     = $validated['kursi'];
        $konflik          = array_intersect($kursiTerisiPergi, $kursiDipesan);

        if (! empty($konflik)) {
            return back()
                ->withInput()
                ->withErrors(['kursi' => 'Kursi nomor ' . implode(', ', $konflik) . ' sudah tidak tersedia.']);
        }

        $isRoundTrip    = ! empty($validated['is_round_trip']);
        $jadwalPulang   = null;
        $hargaTiketPulang = 0;

        if ($isRoundTrip && $validated['jadwal_pulang_id']) {
            $jadwalPulang = Jadwal::with('bus')->findOrFail($validated['jadwal_pulang_id']);

            // Cek kursi pulang
            $kursiTerisiPulang = $jadwalPulang->kursiTerisi();
            $konflikPulang     = array_intersect($kursiTerisiPulang, $kursiDipesan);

            if (! empty($konflikPulang)) {
                return back()
                    ->withInput()
                    ->withErrors(['kursi' => 'Kursi nomor ' . implode(', ', $konflikPulang) . ' sudah tidak tersedia untuk jadwal pulang.']);
            }

            $hargaTiketPulang = (float) $jadwalPulang->harga_tiket;
        }

        $jumlahKursi  = count($kursiDipesan);
        $hargaPergi   = (float) $jadwal->harga_tiket;
        $totalBayar   = ($hargaPergi * $jumlahKursi) + ($hargaTiketPulang * ($isRoundTrip ? $jumlahKursi : 0));

        // Tentukan status pembayaran
        $statusPembayaran = $validated['metode_pembayaran'] === 'Cash' ? 'lunas' : 'pending';

        DB::beginTransaction();

        try {
            // Buat Pemesanan
            $pemesanan = Pemesanan::create([
                'jadwal_id'         => $validated['jadwal_id'],
                'jadwal_pulang_id'  => $isRoundTrip ? $validated['jadwal_pulang_id'] : null,
                'tipe_pemesanan'    => 'Sales_Pool',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'total_bayar'       => $totalBayar,
                'is_round_trip'     => $isRoundTrip,
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp_pemesan'     => $validated['no_hp_pemesan'],
                'tanggal_transaksi' => now(),
                'sales_id'          => Auth::id(),
                'status_pembayaran' => $statusPembayaran,
            ]);

            // Buat Penumpang untuk setiap kursi (jadwal pergi)
            foreach ($kursiDipesan as $index => $nomorKursi) {
                Penumpang::create([
                    'pemesanan_id'  => $pemesanan->id,
                    'jadwal_id'     => $validated['jadwal_id'],
                    'nomor_kursi'   => $nomorKursi,
                    'nama_penumpang' => $validated['nama_penumpang'][$index],
                ]);
            }

            // Jika round trip: buat Penumpang untuk jadwal pulang
            if ($isRoundTrip && $jadwalPulang) {
                foreach ($kursiDipesan as $index => $nomorKursi) {
                    Penumpang::create([
                        'pemesanan_id'  => $pemesanan->id,
                        'jadwal_id'     => $jadwalPulang->id,
                        'nomor_kursi'   => $nomorKursi,
                        'nama_penumpang' => $validated['nama_penumpang'][$index],
                    ]);
                }
            }

            DB::commit();

            // Load relasi untuk view sukses
            $pemesanan->load([
                'jadwal.rute',
                'jadwal.bus',
                'jadwalPulang.rute',
                'jadwalPulang.bus',
                'penumpangsPergi',
                'penumpangsPulang',
            ]);

            return view('sales.pemesanan.sukses', compact('pemesanan'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.']);
        }
    }

    /**
     * API endpoint: return JSON daftar nomor kursi yang sudah terisi.
     */
    public function getKursiTerisi(int $jadwalId)
    {
        $jadwal = Jadwal::with('penumpangs')->findOrFail($jadwalId);

        return response()->json([
            'success'      => true,
            'jadwal_id'    => $jadwal->id,
            'kursi_terisi' => $jadwal->kursiTerisi(),
            'jumlah_kursi' => $jadwal->bus->jumlah_kursi,
        ]);
    }

    /**
     * Dapatkan daftar jadwal pulang (asal-tujuan terbalik dari jadwal pergi)
     * setelah tanggal keberangkatan pergi.
     */
    public function getJadwalPulang(Request $request)
    {
        $request->validate([
            'jadwal_pergi_id' => 'required|exists:jadwals,id',
        ]);

        $jadwalPergi = Jadwal::with('rute')->findOrFail($request->jadwal_pergi_id);

        $jadwalPulangList = Jadwal::with(['bus', 'rute', 'penumpangs'])
            ->whereHas('rute', function ($q) use ($jadwalPergi) {
                $q->where('asal', $jadwalPergi->rute->tujuan)
                  ->where('tujuan', $jadwalPergi->rute->asal);
            })
            ->whereDate('tanggal_berangkat', '>=', $jadwalPergi->tanggal_berangkat)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get()
            ->map(function ($jadwal) {
                return [
                    'id'                => $jadwal->id,
                    'tanggal_berangkat' => $jadwal->tanggal_berangkat->format('d/m/Y'),
                    'waktu_berangkat'   => $jadwal->waktu_berangkat,
                    'rute'              => $jadwal->rute->asal . ' → ' . $jadwal->rute->tujuan,
                    'tipe_bus'          => $jadwal->bus->tipe_bus,
                    'harga_tiket'       => $jadwal->harga_tiket,
                    'kursi_terisi'      => $jadwal->kursiTerisi(),
                    'jumlah_kursi'      => $jadwal->bus->jumlah_kursi,
                    'kursi_tersedia'    => $jadwal->bus->jumlah_kursi - count($jadwal->kursiTerisi()),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $jadwalPulangList,
        ]);
    }
}
