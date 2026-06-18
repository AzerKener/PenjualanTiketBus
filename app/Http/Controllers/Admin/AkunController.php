<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    /**
     * Tampilkan profil akun user yang sedang login.
     * Read-only — tidak ada form edit/update.
     */
    public function index()
    {
        $user = Auth::user();

        return view('admin.akun.index', compact('user'));
    }
}
