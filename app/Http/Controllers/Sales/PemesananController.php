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
    public function index()
    {
        $asalList   = Rute::select('asal')->distinct()->orderBy('asal')->pluck('asal');
        $tujuanList = Rute::select('tujuan')->distinct()->orderBy('tujuan')->pluck('tujuan');
        $tipesBus   = ['Ekonomi', 'Bisnis', 'Eksekutif'];

        return view('sales.pemesanan.index', compact('asalList', 'tujuanList', 'tipesBus'));
    }

    public function cariJadwal(Request $request)
    {
        $request->validate([
            'asal'              => 'required|string|max:100',
            'tujuan'            => 'required|string|max:100',
            'tanggal_berangkat' => 'required|date|after_or_equal:today',
            'tipe_bus'          => 'nullable|string|in:Ekonomi,Bisnis,Eksekutif',
        ]);

        $pegawai = \App\Models\Pegawai::where('user_id', Auth::id())->first();

        $query = Jadwal::with(['bus', 'rute', 'pool', 'penumpangs'])
            ->whereHas('rute', function ($q) use ($request) {
                $q->where('asal', $request->asal)
                  ->where('tujuan', $request->tujuan);
            })
            ->whereDate('tanggal_berangkat', $request->tanggal_berangkat)
            ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [now()->addMinutes(60)])
            ->whereNotIn('status', ['selesai', 'dibatalkan']);

        if ($pegawai && $pegawai->pool_id) {
            $query->where('pool_id', $pegawai->pool_id);
        }

        if ($request->filled('tipe_bus')) {
            $query->whereHas('bus', fn($q) => $q->where('tipe_bus', $request->tipe_bus));
        }

        $jadwals = $query->orderBy('waktu_berangkat')->get();
        $jadwals->each(function ($j) {
            $j->kursi_terisi   = $j->kursiTerisi();
            $j->kursi_tersedia = $j->bus->jumlah_kursi - count($j->kursi_terisi);
        });

        $asalList   = Rute::select('asal')->distinct()->orderBy('asal')->pluck('asal');
        $tujuanList = Rute::select('tujuan')->distinct()->orderBy('tujuan')->pluck('tujuan');
        $tipesBus   = ['Ekonomi', 'Bisnis', 'Eksekutif'];

        return view('sales.pemesanan.index', compact('jadwals', 'asalList', 'tujuanList', 'tipesBus'))
            ->with('search', $request->only('asal', 'tujuan', 'tanggal_berangkat', 'tipe_bus'));
    }

    public function pilihJadwal(int $id)
    {
        $jadwal = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek', 'penumpangs'])
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->findOrFail($id);

        $kursiTerisi = $jadwal->kursiTerisi();

        // Generate kursi format alfanumerik (1A, 1B, 1C, 1D, ...) — sama dengan User
        $jumlahKursi = $jadwal->bus->jumlah_kursi;
        $cols = ['A', 'B', 'C', 'D'];
        $semuaKursi = [];
        for ($row = 1; $row <= ceil($jumlahKursi / 4); $row++) {
            foreach ($cols as $col) {
                if (count($semuaKursi) < $jumlahKursi) {
                    $semuaKursi[] = $row . $col;
                }
            }
        }

        $jadwalPulangList = Jadwal::with(['bus', 'rute'])
            ->whereHas('rute', fn($q) => $q->where('asal', $jadwal->rute->tujuan)
                                           ->where('tujuan', $jadwal->rute->asal))
            ->whereDate('tanggal_berangkat', '>', $jadwal->tanggal_berangkat)
            ->whereRaw("CONCAT(tanggal_berangkat, ' ', waktu_berangkat) > ?", [now()->addMinutes(60)])
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('tanggal_berangkat')->orderBy('waktu_berangkat')
            ->get();

        return view('sales.pemesanan.pilih-jadwal', compact(
            'jadwal', 'kursiTerisi', 'semuaKursi', 'jadwalPulangList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id'          => 'required|exists:jadwals,id',
            'nama_pemesan'       => 'required|string|max:100',
            'no_hp_pemesan'      => 'required|string|max:20',
            'kursi'              => 'required|array|min:1',
            'kursi.*'            => 'required|string|max:10',
            'nama_penumpang'     => 'required|array|min:1',
            'nama_penumpang.*'   => 'required|string|max:100',
            'is_round_trip'      => 'nullable|boolean',
            'jadwal_pulang_id'   => 'nullable|required_if:is_round_trip,1|exists:jadwals,id',
        ]);

        if (count($validated['kursi']) !== count($validated['nama_penumpang'])) {
            return back()->withInput()->withErrors(['kursi' => 'Jumlah kursi dan nama penumpang harus sama.']);
        }

        $jadwal = Jadwal::with('bus')->findOrFail($validated['jadwal_id']);

        $konflik = array_intersect($jadwal->kursiTerisi(), $validated['kursi']);
        if (!empty($konflik)) {
            return back()->withInput()->withErrors(['kursi' => 'Kursi nomor ' . implode(', ', $konflik) . ' sudah tidak tersedia.']);
        }

        $isRoundTrip    = !empty($validated['is_round_trip']);
        $jadwalPulang   = null;
        $hargaTiketPulang = 0;

        if ($isRoundTrip && !empty($validated['jadwal_pulang_id'])) {
            $jadwalPulang = Jadwal::with('bus')->findOrFail($validated['jadwal_pulang_id']);
            $konflikPulang = array_intersect($jadwalPulang->kursiTerisi(), $validated['kursi']);
            if (!empty($konflikPulang)) {
                return back()->withInput()->withErrors(['kursi' => 'Kursi nomor ' . implode(', ', $konflikPulang) . ' tidak tersedia untuk jadwal pulang.']);
            }
            $hargaTiketPulang = (float) $jadwalPulang->harga_tiket;
        }

        $jumlahKursi = count($validated['kursi']);
        $hargaPergi  = (float) $jadwal->harga_tiket;
        $totalBayar  = ($hargaPergi * $jumlahKursi) + ($isRoundTrip ? $hargaTiketPulang * $jumlahKursi : 0);

        DB::beginTransaction();
        try {
            $matchedUser = \App\Models\User::where('no_hp', $validated['no_hp_pemesan'])->first();
            $pemesanan = Pemesanan::create([
                'jadwal_id'         => $jadwal->id,
                'jadwal_pulang_id'  => $jadwalPulang?->id,
                'tipe_pemesanan'    => 'Sales_Pool',
                'metode_pembayaran' => 'Cash',
                'total_bayar'       => $totalBayar,
                'is_round_trip'     => $isRoundTrip,
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp_pemesan'     => $validated['no_hp_pemesan'],
                'tanggal_transaksi' => now(),
                'sales_id'          => Auth::id(),
                'status_pembayaran' => 'lunas',
                'user_id'           => $matchedUser?->id,
            ]);

            foreach ($validated['kursi'] as $i => $nomorKursi) {
                Penumpang::create([
                    'pemesanan_id'   => $pemesanan->id,
                    'jadwal_id'      => $validated['jadwal_id'],
                    'nomor_kursi'    => $nomorKursi,
                    'nama_penumpang' => $validated['nama_penumpang'][$i],
                ]);
            }

            if ($isRoundTrip && $jadwalPulang) {
                foreach ($validated['kursi'] as $i => $nomorKursi) {
                    Penumpang::create([
                        'pemesanan_id'   => $pemesanan->id,
                        'jadwal_id'      => $jadwalPulang->id,
                        'nomor_kursi'    => $nomorKursi,
                        'nama_penumpang' => $validated['nama_penumpang'][$i],
                    ]);
                }
            }

            DB::commit();

            if ($pemesanan->user) {
                $pemesanan->user->notify(new \App\Notifications\UpdateStatusTiket($pemesanan, 'lunas'));
            }

            $pemesanan->load(['jadwal.rute', 'jadwal.bus', 'jadwalPulang.rute', 'jadwalPulang.bus', 'penumpangsPergi', 'penumpangsPulang']);

            return redirect()->route('sales.pemesanan.sukses', $pemesanan->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan. Silakan coba lagi.']);
        }
    }

    public function sukses(Pemesanan $pemesanan)
    {
        // Pastikan pemesanan ini milik sales yang login
        abort_if($pemesanan->sales_id !== Auth::id(), 403);
        $pemesanan->load(['jadwal.rute', 'jadwal.bus', 'jadwalPulang.rute', 'jadwalPulang.bus', 'penumpangsPergi', 'penumpangsPulang']);
        return view('sales.pemesanan.sukses', compact('pemesanan'));
    }

    public function getKursiTerisi(int $jadwalId)
    {
        $jadwal = Jadwal::with('penumpangs')->findOrFail($jadwalId);
        return response()->json([
            'success'      => true,
            'kursi_terisi' => $jadwal->kursiTerisi(),
            'jumlah_kursi' => $jadwal->bus->jumlah_kursi,
        ]);
    }

    public function getJadwalPulang(Request $request)
    {
        $request->validate(['jadwal_pergi_id' => 'required|exists:jadwals,id']);
        $jadwalPergi = Jadwal::with('rute')->findOrFail($request->jadwal_pergi_id);

        $list = Jadwal::with(['bus', 'rute', 'penumpangs'])
            ->whereHas('rute', fn($q) => $q->where('asal', $jadwalPergi->rute->tujuan)
                                           ->where('tujuan', $jadwalPergi->rute->asal))
            ->whereDate('tanggal_berangkat', '>=', $jadwalPergi->tanggal_berangkat)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('tanggal_berangkat')->orderBy('waktu_berangkat')
            ->get()
            ->map(fn($j) => [
                'id'                => $j->id,
                'tanggal_berangkat' => $j->tanggal_berangkat->format('d/m/Y'),
                'waktu_berangkat'   => $j->waktu_berangkat,
                'rute'              => $j->rute->asal . ' → ' . $j->rute->tujuan,
                'tipe_bus'          => $j->bus->tipe_bus,
                'harga_tiket'       => $j->harga_tiket,
                'kursi_terisi'      => $j->kursiTerisi(),
                'jumlah_kursi'      => $j->bus->jumlah_kursi,
                'kursi_tersedia'    => $j->bus->jumlah_kursi - count($j->kursiTerisi()),
            ]);

        return response()->json(['success' => true, 'data' => $list]);
    }
}
