<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::latest()->paginate(10);

        return view('admin.bus.index', compact('buses'));
    }

    public function create()
    {
        $tipeBusOptions = ['Ekonomi', 'VIP', 'Executive'];

        return view('admin.bus.create', compact('tipeBusOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_polisi' => 'required|string|max:20|unique:buses,nomor_polisi',
            'tipe_bus'     => 'required|in:Ekonomi,VIP,Executive',
            'jumlah_kursi' => 'required|integer|min:1|max:100',
        ], [
            'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
            'nomor_polisi.unique'   => 'Nomor polisi sudah terdaftar.',
            'tipe_bus.required'     => 'Tipe bus wajib dipilih.',
            'tipe_bus.in'           => 'Tipe bus tidak valid.',
            'jumlah_kursi.required' => 'Jumlah kursi wajib diisi.',
            'jumlah_kursi.integer'  => 'Jumlah kursi harus berupa angka.',
            'jumlah_kursi.min'      => 'Jumlah kursi minimal 1.',
        ]);

        $validated['fasilitas_bus'] = implode(',', $request->fasilitas_bus ?? []);

        Bus::create($validated);

        return redirect()->route('admin.bus.index')
            ->with('success', 'Bus berhasil ditambahkan.');
    }

    public function getFasilitasAttribute()
{
    return match ($this->tipe_bus) {

        'Ekonomi' => [
            ['icon' => '❄️', 'nama' => 'AC'],
            ['icon' => '🧳', 'nama' => 'Bagasi Gratis 20 Kg'],
            ['icon' => '💧', 'nama' => 'Air Mineral'],
        ],

        'VIP' => [
            ['icon' => '❄️', 'nama' => 'AC'],
            ['icon' => '📶', 'nama' => 'WiFi'],
            ['icon' => '🔌', 'nama' => 'USB Charger'],
            ['icon' => '💺', 'nama' => 'Reclining Seat'],
            ['icon' => '🧳', 'nama' => 'Bagasi Gratis 20 Kg'],
            ['icon' => '💧', 'nama' => 'Air Mineral'],
            ['icon' => '🛏️', 'nama' => 'Selimut'],
        ],

        'Executive' => [
            ['icon' => '❄️', 'nama' => 'AC'],
            ['icon' => '📶', 'nama' => 'WiFi'],
            ['icon' => '🔌', 'nama' => 'USB Charger'],
            ['icon' => '📺', 'nama' => 'TV'],
            ['icon' => '🍱', 'nama' => 'Snack'],
            ['icon' => '🧳', 'nama' => 'Bagasi Gratis 20 Kg'],
            ['icon' => '🚻', 'nama' => 'Toilet'],
            ['icon' => '💺', 'nama' => 'Reclining Seat'],
            ['icon' => '🦶', 'nama' => 'Foot Rest'],
            ['icon' => '💧', 'nama' => 'Air Mineral'],
            ['icon' => '🛏️', 'nama' => 'Selimut'],
            ['icon' => '🛌', 'nama' => 'Bantal'],
        ],

        default => [],
    };
}

    public function edit(Bus $bus)
    {
        $tipeBusOptions = ['Ekonomi', 'VIP', 'Executive'];

        return view('admin.bus.edit', compact('bus', 'tipeBusOptions'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'nomor_polisi' => 'required|string|max:20|unique:buses,nomor_polisi,' . $bus->id,
            'tipe_bus'     => 'required|in:Ekonomi,VIP,Executive',
            'jumlah_kursi' => 'required|integer|min:1|max:100',
        ], [
            'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
            'nomor_polisi.unique'   => 'Nomor polisi sudah terdaftar.',
            'tipe_bus.required'     => 'Tipe bus wajib dipilih.',
            'tipe_bus.in'           => 'Tipe bus tidak valid.',
            'jumlah_kursi.required' => 'Jumlah kursi wajib diisi.',
            'jumlah_kursi.integer'  => 'Jumlah kursi harus berupa angka.',
            'jumlah_kursi.min'      => 'Jumlah kursi minimal 1.',
        ]);

        $validated['fasilitas_bus'] = implode(',', $request->fasilitas_bus ?? []);

        $bus->update($validated);

        return redirect()->route('admin.bus.index')
            ->with('success', 'Data bus berhasil diperbarui.');
    }

    public function destroy(Bus $bus)
    {
        // Cek apakah bus masih digunakan di jadwal aktif
        $jadwalAktif = $bus->jadwals()
            ->whereIn('status', ['menunggu', 'berangkat'])
            ->exists();

        if ($jadwalAktif) {
            return redirect()->route('admin.bus.index')
                ->with('error', 'Bus tidak dapat dihapus karena masih memiliki jadwal aktif.');
        }

        $bus->delete();

        return redirect()->route('admin.bus.index')
            ->with('success', 'Bus berhasil dihapus.');
    }
}
