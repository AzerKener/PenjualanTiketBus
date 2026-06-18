<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    /**
     * Tampilkan profil akun user yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        return view('user.akun.index', compact('user'));
    }
}
