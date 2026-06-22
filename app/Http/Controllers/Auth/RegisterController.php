<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /** Tampilkan form registrasi user online */
    public function showRegisterForm()
    {
        if (Auth::check() && Auth::user()->role === 'User') {
            return redirect()->route('user.home');
        }
        return view('user.auth.register');
    }

    /** Proses registrasi user baru */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'no_hp'                 => ['required', 'string', 'max:20'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'no_hp'    => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'role'     => 'User',
        ]);

        return redirect()->route('user.login')
            ->with('success', 'Registrasi berhasil! Silakan login menggunakan akun yang baru dibuat.');
    }

    /** Tampilkan form login user online */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'User') {
            return redirect()->route('user.home');
        }
        return view('user.auth.login');
    }

    /** Proses login user online */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Pastikan yang login adalah user biasa (bukan staff)
            if ($user->role !== 'User') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan akun pembeli. Gunakan halaman login staff.']);
            }

            return redirect()->intended(route('user.home'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    /** Logout user online */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.home');
    }
}
