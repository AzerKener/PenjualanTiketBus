<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function unread()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications->count()
        ]);
    }

    /**
     * Tampilkan semua notifikasi user yang login.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua notifikasi, urutkan yang terbaru
        $notifikasis = $user->notifications()->paginate(15);
        
        // Tandai semua sebagai sudah dibaca saat halaman ini dibuka
        $user->unreadNotifications->markAsRead();

        // Tentukan layout berdasarkan role user
        $layout = 'layouts.user'; // Default
        switch ($user->role) {
            case 'Admin':
                $layout = 'layouts.admin';
                break;
            case 'Supir':
                $layout = 'layouts.supir';
                break;
            case 'Kenek':
                $layout = 'layouts.kenek';
                break;
            case 'Sales':
                $layout = 'layouts.sales';
                break;
        }

        return view('notifikasi.index', compact('notifikasis', 'layout'));
    }

    /**
     * Hapus semua notifikasi.
     */
    public function clear()
    {
        Auth::user()->notifications()->delete();
        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}
