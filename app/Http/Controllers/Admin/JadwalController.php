<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Jadwal;
use App\Models\Pegawai;
use App\Models\Pool;
use App\Models\Rute;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $pools = Pool::orderBy('nama_pool')->get();
        $rutes = Rute::orderBy('asal')->get();

        $jadwals = Jadwal::with(['bus', 'rute', 'pool', 'supir1', 'supir2', 'kenek'])
            ->when($request->filled('pool_id'), fn ($q) => $q->where('pool_id', $request->pool_id))
            ->when($request->filled('rute_id'), fn ($q) => $q->where('rute_id', $request->rute_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('tanggal'), fn ($q) => $q->whereDate('tanggal_berangkat', $request->tanggal))
            ->latest('tanggal_berangkat')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jadwal.index', compact('jadwals', 'pools', 'rutes'));
    }

    public function create()
    {
        $buses   = Bus::orderBy('nomor_polisi')->get();
        $rutes   = Rute::orderBy('asal')->get();
        $pools   = Pool::orderBy('nama_pool')->get();
        $supirs  = Pegawai::where('role', 'Supir')->orderBy('nama')->get();
        $keneks  = Pegawai::where('role', 'Kenek')->orderBy('nama')->get();

        return view('admin.jadwal.create', compact('buses', 'rutes', 'pools', 'supirs', 'keneks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_id'            => 'required|exists:buses,id',
            'rute_id'           => 'required|exists:rutes,id',
            'pool_id'           => 'required|exists:pools,id',
            'pool_tujuan_id'    => 'required|exists:pools,id|different:pool_id',
            'tanggal_berangkat' => 'required|date|after_or_equal:today',
            'waktu_berangkat'   => 'required|date_format:H:i',
            'estimasi_tiba'     => 'required|date_format:H:i',
            'harga_tiket'       => 'required|numeric|min:1000',
            'supir1_id'         => 'required|exists:pegawais,id',
            'supir2_id'         => 'nullable|exists:pegawais,id|different:supir1_id',
            'kenek_id'          => 'nullable|exists:pegawais,id',
        ], [
            'bus_id.required'                  => 'Bus wajib dipilih.',
            'rute_id.required'                 => 'Rute wajib dipilih.',
            'pool_id.required'                 => 'Pool Asal wajib dipilih.',
            'pool_tujuan_id.required'          => 'Pool Tujuan wajib dipilih.',
            'pool_tujuan_id.different'         => 'Pool Tujuan tidak boleh sama dengan Pool Asal.',
            'tanggal_berangkat.required'       => 'Tanggal berangkat wajib diisi.',
            'tanggal_berangkat.after_or_equal' => 'Tanggal berangkat tidak boleh di masa lampau.',
            'waktu_berangkat.required'         => 'Waktu berangkat wajib diisi.',
            'estimasi_tiba.required'           => 'Estimasi tiba wajib diisi.',
            'harga_tiket.required'             => 'Harga tiket wajib diisi.',
            'harga_tiket.min'                  => 'Harga tiket minimal Rp 1.000.',
            'supir1_id.required'               => 'Supir 1 wajib dipilih.',
            'supir2_id.different'              => 'Supir 2 tidak boleh sama dengan Supir 1.',
        ]);

        // Validasi manual estimasi tiba — support perjalanan melewati tengah malam
        $berangkat    = \Carbon\Carbon::createFromFormat('H:i', $validated['waktu_berangkat']);
        $estimasiTiba = \Carbon\Carbon::createFromFormat('H:i', $validated['estimasi_tiba']);
        // Jika estimasi_tiba <= waktu_berangkat, asumsikan tiba keesokan harinya
        if ($estimasiTiba->lte($berangkat)) {
            $estimasiTiba->addDay();
        }
        if (!$estimasiTiba->greaterThan($berangkat)) {
            return back()->withInput()
                ->withErrors(['estimasi_tiba' => 'Estimasi tiba harus setelah waktu berangkat.']);
        }

        // Status selalu 'menunggu' saat jadwal baru dibuat
        $validated['status'] = 'menunggu';

        $tanggal = $validated['tanggal_berangkat'];

        // --- Validasi konflik jadwal Supir 1 ---
        $supir1 = Pegawai::findOrFail($validated['supir1_id']);
        if ($supir1->hasBentrokanJadwal($tanggal)) {
            return back()->withInput()
                ->withErrors(['supir1_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
        }

        // --- Validasi konflik jadwal Supir 2 (jika diisi) ---
        if (!empty($validated['supir2_id'])) {
            $supir2 = Pegawai::findOrFail($validated['supir2_id']);
            if ($supir2->hasBentrokanJadwal($tanggal)) {
                return back()->withInput()
                    ->withErrors(['supir2_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
            }
        }

        // --- Validasi konflik jadwal Kenek (jika diisi) ---
        if (!empty($validated['kenek_id'])) {
            $kenek = Pegawai::findOrFail($validated['kenek_id']);
            if ($kenek->hasBentrokanJadwal($tanggal)) {
                return back()->withInput()
                    ->withErrors(['kenek_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
            }
        }

        // --- Cek bus tidak bentrok di tanggal yang sama ---
        $busKonflik = Jadwal::where('bus_id', $validated['bus_id'])
            ->where('tanggal_berangkat', $tanggal)
            ->where('status', '!=', 'selesai')
            ->exists();

        if ($busKonflik) {
            return back()->withInput()
                ->withErrors(['bus_id' => 'Bus sudah memiliki jadwal pada tanggal ini dan belum selesai.']);
        }

        Jadwal::create($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $buses   = Bus::orderBy('nomor_polisi')->get();
        $rutes   = Rute::orderBy('asal')->get();
        $pools   = Pool::orderBy('nama_pool')->get();
        $supirs  = Pegawai::where('role', 'Supir')->orderBy('nama')->get();
        $keneks  = Pegawai::where('role', 'Kenek')->orderBy('nama')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'buses', 'rutes', 'pools', 'supirs', 'keneks'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'bus_id'            => 'required|exists:buses,id',
            'rute_id'           => 'required|exists:rutes,id',
            'pool_id'           => 'required|exists:pools,id',
            'pool_tujuan_id'    => 'required|exists:pools,id|different:pool_id',
            'tanggal_berangkat' => 'required|date',
            'waktu_berangkat'   => 'required|date_format:H:i',
            'estimasi_tiba'     => 'required|date_format:H:i',
            'harga_tiket'       => 'required|numeric|min:1000',
            'supir1_id'         => 'required|exists:pegawais,id',
            'supir2_id'         => 'nullable|exists:pegawais,id|different:supir1_id',
            'kenek_id'          => 'nullable|exists:pegawais,id',
        ], [
            'bus_id.required'            => 'Bus wajib dipilih.',
            'rute_id.required'           => 'Rute wajib dipilih.',
            'pool_id.required'           => 'Pool Asal wajib dipilih.',
            'pool_tujuan_id.required'    => 'Pool Tujuan wajib dipilih.',
            'pool_tujuan_id.different'   => 'Pool Tujuan tidak boleh sama dengan Pool Asal.',
            'tanggal_berangkat.required' => 'Tanggal berangkat wajib diisi.',
            'waktu_berangkat.required'   => 'Waktu berangkat wajib diisi.',
            'estimasi_tiba.required'     => 'Estimasi tiba wajib diisi.',
            'harga_tiket.required'       => 'Harga tiket wajib diisi.',
            'harga_tiket.min'            => 'Harga tiket minimal Rp 1.000.',
            'supir1_id.required'         => 'Supir 1 wajib dipilih.',
            'supir2_id.different'        => 'Supir 2 tidak boleh sama dengan Supir 1.',
        ]);

        // Validasi manual estimasi tiba — support perjalanan melewati tengah malam
        $berangkat    = \Carbon\Carbon::createFromFormat('H:i', $validated['waktu_berangkat']);
        $estimasiTiba = \Carbon\Carbon::createFromFormat('H:i', $validated['estimasi_tiba']);
        if ($estimasiTiba->lte($berangkat)) {
            $estimasiTiba->addDay();
        }
        if (!$estimasiTiba->greaterThan($berangkat)) {
            return back()->withInput()
                ->withErrors(['estimasi_tiba' => 'Estimasi tiba harus setelah waktu berangkat.']);
        }

        // Status tidak diubah melalui form edit — gunakan updateStatus()

        $tanggal   = $validated['tanggal_berangkat'];
        $excludeId = $jadwal->id;

        // --- Validasi konflik jadwal Supir 1 (exclude jadwal saat ini) ---
        $supir1 = Pegawai::findOrFail($validated['supir1_id']);
        if ($supir1->hasBentrokanJadwal($tanggal, $excludeId)) {
            return back()->withInput()
                ->withErrors(['supir1_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
        }

        // --- Validasi konflik jadwal Supir 2 (jika diisi, exclude jadwal saat ini) ---
        if (!empty($validated['supir2_id'])) {
            $supir2 = Pegawai::findOrFail($validated['supir2_id']);
            if ($supir2->hasBentrokanJadwal($tanggal, $excludeId)) {
                return back()->withInput()
                    ->withErrors(['supir2_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
            }
        }

        // --- Validasi konflik jadwal Kenek (jika diisi, exclude jadwal saat ini) ---
        if (!empty($validated['kenek_id'])) {
            $kenek = Pegawai::findOrFail($validated['kenek_id']);
            if ($kenek->hasBentrokanJadwal($tanggal, $excludeId)) {
                return back()->withInput()
                    ->withErrors(['kenek_id' => 'Supir/Kenek sudah memiliki jadwal pada tanggal ini dan belum kembali.']);
            }
        }

        // --- Cek bus tidak bentrok, exclude jadwal saat ini ---
        $busKonflik = Jadwal::where('bus_id', $validated['bus_id'])
            ->where('tanggal_berangkat', $tanggal)
            ->where('status', '!=', 'selesai')
            ->where('id', '!=', $excludeId)
            ->exists();

        if ($busKonflik) {
            return back()->withInput()
                ->withErrors(['bus_id' => 'Bus sudah memiliki jadwal pada tanggal ini dan belum selesai.']);
        }

        $jadwal->update($validated);

        // Notify all users who booked this schedule
        $users = \App\Models\User::whereHas('pemesanans', function ($query) use ($jadwal) {
            $query->where('jadwal_id', $jadwal->id)->whereIn('status_pembayaran', ['lunas', 'selesai', 'pending']);
        })->get();

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\PerubahanJadwal($jadwal));
        }

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        // Cegah hapus jadwal yang sudah berangkat atau selesai dan punya penumpang
        if ($jadwal->status !== 'menunggu') {
            return redirect()->route('admin.jadwal.index')
                ->with('error', 'Jadwal yang sudah berangkat atau selesai tidak dapat dihapus.');
        }

        if ($jadwal->penumpangs()->exists()) {
            return redirect()->route('admin.jadwal.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah memiliki data penumpang.');
        }

        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Update status jadwal (menunggu / berangkat / selesai).
     */
    public function updateStatus(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'status' => 'required|in:menunggu,berangkat,selesai',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        // Aturan transisi status
        $transisiDiizinkan = [
            'menunggu'  => ['berangkat'],
            'berangkat' => ['selesai'],
            'selesai'   => [],
        ];

        $statusBaru = $request->status;
        $statusLama = $jadwal->status;

        if (!in_array($statusBaru, $transisiDiizinkan[$statusLama])) {
            return redirect()->route('admin.jadwal.index')
                ->with('error', 'Transisi status dari "' . $statusLama . '" ke "' . $statusBaru . '" tidak diizinkan.');
        }

        $jadwal->update(['status' => $statusBaru]);

        if ($statusBaru === 'selesai' && $statusLama !== 'selesai') {
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

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Status jadwal berhasil diubah menjadi "' . $statusBaru . '".');
    }
}
