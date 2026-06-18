<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    public function index()
    {
        $rutes = Rute::latest()->paginate(10);

        return view('admin.rute.index', compact('rutes'));
    }

    public function create()
    {
        return view('admin.rute.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asal'   => 'required|string|max:100',
            'tujuan' => 'required|string|max:100',
        ], [
            'asal.required'   => 'Asal keberangkatan wajib diisi.',
            'tujuan.required' => 'Tujuan keberangkatan wajib diisi.',
        ]);

        // Cek duplikat rute asal-tujuan
        $exists = Rute::where('asal', $validated['asal'])
            ->where('tujuan', $validated['tujuan'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['asal' => 'Rute dari ' . $validated['asal'] . ' ke ' . $validated['tujuan'] . ' sudah ada.']);
        }

        Rute::create($validated);

        return redirect()->route('admin.rute.index')
            ->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(Rute $rute)
    {
        return view('admin.rute.edit', compact('rute'));
    }

    public function update(Request $request, Rute $rute)
    {
        $validated = $request->validate([
            'asal'   => 'required|string|max:100',
            'tujuan' => 'required|string|max:100',
        ], [
            'asal.required'   => 'Asal keberangkatan wajib diisi.',
            'tujuan.required' => 'Tujuan keberangkatan wajib diisi.',
        ]);

        // Cek duplikat, kecuali rute saat ini
        $exists = Rute::where('asal', $validated['asal'])
            ->where('tujuan', $validated['tujuan'])
            ->where('id', '!=', $rute->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['asal' => 'Rute dari ' . $validated['asal'] . ' ke ' . $validated['tujuan'] . ' sudah ada.']);
        }

        $rute->update($validated);

        return redirect()->route('admin.rute.index')
            ->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(Rute $rute)
    {
        // Cek apakah rute masih digunakan di jadwal
        $jadwalAktif = $rute->jadwals()
            ->whereIn('status', ['menunggu', 'berangkat'])
            ->exists();

        if ($jadwalAktif) {
            return redirect()->route('admin.rute.index')
                ->with('error', 'Rute tidak dapat dihapus karena masih digunakan pada jadwal aktif.');
        }

        $rute->delete();

        return redirect()->route('admin.rute.index')
            ->with('success', 'Rute berhasil dihapus.');
    }
}
