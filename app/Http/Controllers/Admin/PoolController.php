<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index()
    {
        $pools = Pool::withCount(['pegawais', 'jadwals'])->latest()->paginate(10);

        return view('admin.pool.index', compact('pools'));
    }

    public function create()
    {
        return view('admin.pool.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pool' => 'required|string|max:100|unique:pools,nama_pool',
            'lokasi'    => 'required|string|max:255',
        ], [
            'nama_pool.required' => 'Nama pool wajib diisi.',
            'nama_pool.unique'   => 'Nama pool sudah terdaftar.',
            'lokasi.required'    => 'Lokasi pool wajib diisi.',
        ]);

        Pool::create($validated);

        return redirect()->route('admin.pool.index')
            ->with('success', 'Pool berhasil ditambahkan.');
    }

    public function edit(Pool $pool)
    {
        return view('admin.pool.edit', compact('pool'));
    }

    public function update(Request $request, Pool $pool)
    {
        $validated = $request->validate([
            'nama_pool' => 'required|string|max:100|unique:pools,nama_pool,' . $pool->id,
            'lokasi'    => 'required|string|max:255',
        ], [
            'nama_pool.required' => 'Nama pool wajib diisi.',
            'nama_pool.unique'   => 'Nama pool sudah terdaftar.',
            'lokasi.required'    => 'Lokasi pool wajib diisi.',
        ]);

        $pool->update($validated);

        return redirect()->route('admin.pool.index')
            ->with('success', 'Data pool berhasil diperbarui.');
    }

    public function destroy(Pool $pool)
    {
        // Cek apakah pool masih memiliki pegawai atau jadwal aktif
        if ($pool->pegawais()->exists()) {
            return redirect()->route('admin.pool.index')
                ->with('error', 'Pool tidak dapat dihapus karena masih memiliki pegawai terdaftar.');
        }

        $jadwalAktif = $pool->jadwals()
            ->whereIn('status', ['menunggu', 'berangkat'])
            ->exists();

        if ($jadwalAktif) {
            return redirect()->route('admin.pool.index')
                ->with('error', 'Pool tidak dapat dihapus karena masih memiliki jadwal aktif.');
        }

        $pool->delete();

        return redirect()->route('admin.pool.index')
            ->with('success', 'Pool berhasil dihapus.');
    }
}
