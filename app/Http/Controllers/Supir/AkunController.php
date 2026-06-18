<?php

namespace App\Http\Controllers\Supir;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('supir.akun.index', compact('user'));
    }
}
