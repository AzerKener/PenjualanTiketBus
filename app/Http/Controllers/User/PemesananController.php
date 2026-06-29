<?php

namespace App\Http\Controllers\User;

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
    /** Tampilkan halaman pemilihan kursi untuk jadwal tertentu */
    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['bus', 'rute', 'pool']);

        $kursiTerisi = Penumpang::where('jadwal_id', $jadwal->id)
            ->pluck('nomor_kursi')
            ->toArray();

        $jumlahKursi = $jadwal->bus->jumlah_kursi;

        // Generate daftar kursi (format: 1A, 1B, 1C, 1D, ...)
        $semuaKursi = [];
        $cols = ['A', 'B', 'C', 'D'];
        for ($row = 1; $row <= ceil($jumlahKursi / 4); $row++) {
            foreach ($cols as $col) {
                $no = $row . $col;
                if (count($semuaKursi) < $jumlahKursi) {
                    $semuaKursi[] = $no;
                }
            }
        }

        // Jadwal pulang (rute terbalik, setelah tanggal pergi)
        $jadwalPulangList = Jadwal::with(['bus', 'pool'])
            ->whereHas('rute', fn ($q) => $q->where('asal', $jadwal->rute->tujuan)
                ->where('tujuan', $jadwal->rute->asal))
            ->where('tanggal_berangkat', '>=', $jadwal->tanggal_berangkat)
            ->where('status', 'menunggu')
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get();

        return view('user.pesan', compact('jadwal', 'kursiTerisi', 'semuaKursi', 'jadwalPulangList'));
    }

    /** Simpan pemesanan online */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id'              => ['required', 'exists:jadwals,id'],
            'is_round_trip'          => ['nullable', 'boolean'],
            'jadwal_pulang_id'       => ['nullable', 'required_if:is_round_trip,1', 'exists:jadwals,id'],
            'kursi_pergi'            => ['required', 'array', 'min:1'],
            'kursi_pergi.*'          => ['required', 'string'],
            'nama_penumpang_pergi'   => ['required', 'array', 'min:1'],
            'nama_penumpang_pergi.*' => ['required', 'string', 'max:100'],
            'metode_pembayaran'      => ['required', 'in:Transfer,E-Wallet'],
        ], [
            'jadwal_id.required'           => 'Jadwal tidak valid.',
            'kursi_pergi.required'         => 'Pilih minimal 1 kursi.',
            'nama_penumpang_pergi.required'=> 'Nama penumpang wajib diisi.',
            'metode_pembayaran.required'   => 'Pilih metode pembayaran.',
            'metode_pembayaran.in'         => 'Metode pembayaran tidak valid.',
        ]);

        $jadwalPergi = Jadwal::findOrFail($request->jadwal_id);
        $isRoundTrip = $request->boolean('is_round_trip');
        $jadwalPulang = $isRoundTrip && $request->jadwal_pulang_id
            ? Jadwal::findOrFail($request->jadwal_pulang_id)
            : null;

        // Validasi kursi masih tersedia (race condition prevention)
        $kursiSudahTerisi = Penumpang::where('jadwal_id', $jadwalPergi->id)
            ->whereIn('nomor_kursi', $request->kursi_pergi)
            ->pluck('nomor_kursi');
        if ($kursiSudahTerisi->isNotEmpty()) {
            return back()->withErrors(['kursi_pergi' => 'Kursi ' . $kursiSudahTerisi->implode(', ') . ' sudah dipesan orang lain.'])->withInput();
        }

        // Validasi kursi pulang
        if ($jadwalPulang && $request->filled('kursi_pulang')) {
            $kursiPulangTerisi = Penumpang::where('jadwal_id', $jadwalPulang->id)
                ->whereIn('nomor_kursi', $request->kursi_pulang)
                ->pluck('nomor_kursi');
            if ($kursiPulangTerisi->isNotEmpty()) {
                return back()->withErrors(['kursi_pulang' => 'Kursi pulang ' . $kursiPulangTerisi->implode(', ') . ' sudah dipesan orang lain.'])->withInput();
            }
        }

        $jumlahKursi     = count($request->kursi_pergi);
        $hargaPergi      = $jadwalPergi->harga_tiket * $jumlahKursi;
        $hargaPulang     = $jadwalPulang ? ($jadwalPulang->harga_tiket * $jumlahKursi) : 0;
        $totalBayar      = $hargaPergi + $hargaPulang;
        $statusPembayaran= 'pending';

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::create([
                'jadwal_id'          => $jadwalPergi->id,
                'jadwal_pulang_id'   => $jadwalPulang?->id,
                'tipe_pemesanan'     => 'Online',
                'metode_pembayaran'  => $request->metode_pembayaran,
                'total_bayar'        => $totalBayar,
                'is_round_trip'      => $isRoundTrip,
                'nama_pemesan'       => Auth::user()->name,
                'no_hp_pemesan'      => Auth::user()->no_hp,
                'tanggal_transaksi'  => now(),
                'user_id'            => Auth::id(),
                'status_pembayaran'  => $statusPembayaran,
            ]);

            // Penumpang pergi
            foreach ($request->kursi_pergi as $i => $kursi) {
                Penumpang::create([
                    'pemesanan_id'   => $pemesanan->id,
                    'jadwal_id'      => $jadwalPergi->id,
                    'nomor_kursi'    => $kursi,
                    'nama_penumpang' => $request->nama_penumpang_pergi[$i] ?? Auth::user()->name,
                ]);
            }

            // Penumpang pulang (round trip)
            if ($jadwalPulang && $request->filled('kursi_pulang')) {
                foreach ($request->kursi_pulang as $i => $kursi) {
                    Penumpang::create([
                        'pemesanan_id'   => $pemesanan->id,
                        'jadwal_id'      => $jadwalPulang->id,
                        'nomor_kursi'    => $kursi,
                        'nama_penumpang' => $request->nama_penumpang_pulang[$i] ?? Auth::user()->name,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('user.pesan.sukses', $pemesanan->id);
    }

    /** Halaman sukses pemesanan */
    public function sukses(Pemesanan $pemesanan)
    {
        // Pastikan hanya pemilik pemesanan yang bisa lihat
        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        $pemesanan->load(['jadwal.rute', 'jadwal.bus', 'jadwal.pool', 'jadwalPulang.rute', 'jadwalPulang.bus', 'penumpangs']);

        return view('user.sukses', compact('pemesanan'));
    }

    /** API: Cek Status Pembayaran (Polling) */
    public function cekStatus(Pemesanan $pemesanan)
    {
        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'status_pembayaran' => $pemesanan->status_pembayaran
        ]);
    }

    /** Riwayat pemesanan user */
    public function riwayat()
    {
        $pemesanans = Pemesanan::with(['jadwal.rute', 'jadwal.bus', 'jadwalPulang.rute', 'penumpangs'])
            ->where('user_id', Auth::id())
            ->latest('tanggal_transaksi')
            ->paginate(10);

        return view('user.riwayat', compact('pemesanans'));
    }

    /** E-Ticket view */
    public function etiket(Pemesanan $pemesanan)
    {
        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        $pemesanan->load(['jadwal.rute', 'jadwal.bus', 'jadwal.pool', 'jadwalPulang.rute', 'jadwalPulang.bus', 'penumpangs']);

        return view('user.etiket', compact('pemesanan'));
    }

    /** API: Kursi terisi untuk jadwal */
    public function getKursiTerisi(Jadwal $jadwal)
    {
        return response()->json([
            'kursi_terisi'  => Penumpang::where('jadwal_id', $jadwal->id)->pluck('nomor_kursi'),
            'jumlah_kursi'  => $jadwal->bus->jumlah_kursi,
        ]);
    }

    /** API: Jadwal pulang */
    public function getJadwalPulang(Request $request)
    {
        $request->validate([
            'asal'    => 'required|string',
            'tujuan'  => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $jadwals = Jadwal::with(['bus', 'pool', 'penumpangs'])
            ->whereHas('rute', fn ($q) => $q->where('asal', $request->tujuan)->where('tujuan', $request->asal))
            ->where('tanggal_berangkat', '>=', $request->tanggal)
            ->where('status', 'menunggu')
            ->orderBy('tanggal_berangkat')
            ->orderBy('waktu_berangkat')
            ->get()
            ->map(fn ($j) => [
                'id'                => $j->id,
                'tanggal_berangkat' => $j->tanggal_berangkat,
                'waktu_berangkat'   => $j->waktu_berangkat,
                'harga_tiket'       => $j->harga_tiket,
                'bus'               => ['nomor_polisi' => $j->bus->nomor_polisi, 'tipe_bus' => $j->bus->tipe_bus, 'jumlah_kursi' => $j->bus->jumlah_kursi],
                'pool'              => ['nama_pool' => $j->pool->nama_pool],
                'kursi_terisi'      => $j->penumpangs->pluck('nomor_kursi'),
                'kursi_tersedia'    => $j->bus->jumlah_kursi - $j->penumpangs->count(),
            ]);

        return response()->json($jadwals);
    }
}
