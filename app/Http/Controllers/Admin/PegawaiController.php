<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with('pool');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
    }

        $pegawais = $query->latest()->paginate(10);

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
            'email'   => 'required|email|unique:users,email',
            'password'=> 'required|string|min:6',
        ], [
            'nama.required'    => 'Nama pegawai wajib diisi.',
            'role.required'    => 'Role pegawai wajib dipilih.',
            'role.in'          => 'Role yang dipilih tidak valid.',
            'pool_id.required' => 'Pool pegawai wajib dipilih.',
            'pool_id.exists'   => 'Pool yang dipilih tidak valid.',
            'no_hp.required'   => 'Nomor HP wajib diisi.',
            'no_hp.regex'      => 'Format nomor HP tidak valid.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah terdaftar.',
            'password.required'=> 'Password wajib diisi.',
            'password.min'     => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['nama'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
                'pool_id'  => $validated['pool_id'],
                'no_hp'    => $validated['no_hp'],
            ]);

            Pegawai::create([
                'nama'    => $validated['nama'],
                'role'    => $validated['role'],
                'pool_id' => $validated['pool_id'],
                'no_hp'   => $validated['no_hp'],
                'user_id' => $user->id,
            ]);
        });

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
            'email'   => 'required|email|unique:users,email,' . ($pegawai->user_id ?? 'NULL'),
            'password'=> 'nullable|string|min:6',
        ], [
            'nama.required'    => 'Nama pegawai wajib diisi.',
            'role.required'    => 'Role pegawai wajib dipilih.',
            'role.in'          => 'Role yang dipilih tidak valid.',
            'pool_id.required' => 'Pool pegawai wajib dipilih.',
            'pool_id.exists'   => 'Pool yang dipilih tidak valid.',
            'no_hp.required'   => 'Nomor HP wajib diisi.',
            'no_hp.regex'      => 'Format nomor HP tidak valid.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah terdaftar.',
            'password.min'     => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($validated, $pegawai) {
            $pegawai->update([
                'nama'    => $validated['nama'],
                'role'    => $validated['role'],
                'pool_id' => $validated['pool_id'],
                'no_hp'   => $validated['no_hp'],
            ]);

            if ($pegawai->user) {
                $userData = [
                    'name'    => $validated['nama'],
                    'email'   => $validated['email'],
                    'role'    => $validated['role'],
                    'pool_id' => $validated['pool_id'],
                    'no_hp'   => $validated['no_hp'],
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $pegawai->user->update($userData);
            } else {
                // If it doesn't have a user, create one (backwards compatibility)
                $user = User::create([
                    'name'     => $validated['nama'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'role'     => $validated['role'],
                    'pool_id'  => $validated['pool_id'],
                    'no_hp'    => $validated['no_hp'],
                ]);
                $pegawai->update(['user_id' => $user->id]);
            }
        });

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

        DB::transaction(function () use ($pegawai) {
            $user = $pegawai->user;
            $pegawai->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil dihapus.');
    }
}
