<?php

namespace App\Http\Controllers\Kenek;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('kenek.akun.index', compact('user'));
    }
}
