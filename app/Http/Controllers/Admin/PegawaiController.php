<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pool;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with('pool')->latest()->paginate(10);

        return view('admin.pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        $pools      = Pool::orderBy('nama_pool')->get();
        $roleOptions = ['Supir', 'Kenek', 'Sales', 'Admin'];

        return view('admin.pegawai.create', compact('pools', 'roleOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:100',
            'role'    => 'required|in:Supir,Kenek,Sales,Admin',
            'pool_id' => 'required|exists:pools,id',
            'no_hp'   => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
        ], [
            'nama.required'    => 'Nama pegawai wajib diisi.',
            'role.required'    => 'Role pegawai wajib dipilih.',
            'role.in'          => 'Role yang dipilih tidak valid.',
            'pool_id.required' => 'Pool pegawai wajib dipilih.',
            'pool_id.exists'   => 'Pool yang dipilih tidak valid.',
            'no_hp.required'   => 'Nomor HP wajib diisi.',
            'no_hp.regex'      => 'Format nomor HP tidak valid.',
        ]);

        Pegawai::create($validated);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        $pools      = Pool::orderBy('nama_pool')->get();
        $roleOptions = ['Supir', 'Kenek', 'Sales', 'Admin'];

        return view('admin.pegawai.edit', compact('pegawai', 'pools', 'roleOptions'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:100',
            'role'    => 'required|in:Supir,Kenek,Sales,Admin',
            'pool_id' => 'required|exists:pools,id',
            'no_hp'   => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
        ], [
            'nama.required'    => 'Nama pegawai wajib diisi.',
            'role.required'    => 'Role pegawai wajib dipilih.',
            'role.in'          => 'Role yang dipilih tidak valid.',
            'pool_id.required' => 'Pool pegawai wajib dipilih.',
            'pool_id.exists'   => 'Pool yang dipilih tidak valid.',
            'no_hp.required'   => 'Nomor HP wajib diisi.',
            'no_hp.regex'      => 'Format nomor HP tidak valid.',
        ]);

        $pegawai->update($validated);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        // Cek apakah pegawai masih punya jadwal aktif (sebagai supir atau kenek)
        $jadwalAktif = \App\Models\Jadwal::where(function ($q) use ($pegawai) {
                $q->where('supir1_id', $pegawai->id)
                  ->orWhere('supir2_id', $pegawai->id)
                  ->orWhere('kenek_id', $pegawai->id);
            })
            ->whereIn('status', ['menunggu', 'berangkat'])
            ->exists();

        if ($jadwalAktif) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai tidak dapat dihapus karena masih memiliki jadwal aktif.');
        }

        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil dihapus.');
    }
}
