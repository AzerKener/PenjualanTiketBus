<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('sales.akun.index', compact('user'));
    }
}
