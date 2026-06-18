<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Sales;
use App\Http\Controllers\Supir;
use App\Http\Controllers\User;

// ─── Root / Landing Page ──────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'Admin' => redirect()->route('admin.dashboard'),
            'Sales' => redirect()->route('sales.dashboard'),
            'Supir' => redirect()->route('supir.dashboard'),
            default => redirect()->route('user.home'),
        };
    }
    return redirect()->route('user.home');
});

// ─── Auth Routes (Staff: Admin / Sales / Supir) ───────────────────────────────
Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Auth Routes (User Online) ────────────────────────────────────────────────
Route::get('/daftar',  [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/daftar', [RegisterController::class, 'register'])->name('register.post');
Route::get('/masuk',   [RegisterController::class, 'showLoginForm'])->name('user.login');
Route::post('/masuk',  [RegisterController::class, 'login'])->name('user.login.post');
Route::post('/keluar', [RegisterController::class, 'logout'])->name('user.logout');

// ─── User (Pembeli Online) Routes ─────────────────────────────────────────────
Route::prefix('tiket')->name('user.')->group(function () {
    Route::get('/',     [User\HomeController::class, 'index'])->name('home');
    Route::get('/cari', [User\HomeController::class, 'cari'])->name('cari');

    Route::middleware(['auth', 'role:User'])->group(function () {
        Route::get('/pesan/{jadwal}',            [User\PemesananController::class, 'show'])->name('pesan.show');
        Route::post('/pesan',                    [User\PemesananController::class, 'store'])->name('pesan.store');
        Route::get('/pesan/sukses/{pemesanan}',  [User\PemesananController::class, 'sukses'])->name('pesan.sukses');
        Route::get('/riwayat',                   [User\PemesananController::class, 'riwayat'])->name('riwayat');
        Route::get('/tiket/{pemesanan}',         [User\PemesananController::class, 'etiket'])->name('etiket');
        Route::get('/kursi-terisi/{jadwal}',     [User\PemesananController::class, 'getKursiTerisi'])->name('kursiTerisi');
        Route::post('/jadwal-pulang',            [User\PemesananController::class, 'getJadwalPulang'])->name('jadwalPulang');
    });
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('bus',     Admin\BusController::class)->parameters(['bus' => 'bus']);
    Route::resource('rute',    Admin\RuteController::class);
    Route::resource('pool',    Admin\PoolController::class);
    Route::resource('pegawai', Admin\PegawaiController::class);
    Route::resource('jadwal',  Admin\JadwalController::class);

    Route::patch('/jadwal/{jadwal}/status', [Admin\JadwalController::class, 'updateStatus'])->name('jadwal.updateStatus');

    Route::get('/penumpang', [Admin\PenumpangController::class, 'index'])->name('penumpang.index');
    Route::get('/laporan',   [Admin\LaporanController::class,   'index'])->name('laporan.index');
    Route::get('/transaksi', [Admin\TransaksiController::class,  'index'])->name('transaksi.index');
    Route::patch('/transaksi/{pemesanan}/konfirmasi', [Admin\TransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
    Route::get('/akun',      [Admin\AkunController::class,       'index'])->name('akun.index');
});

// ─── Sales Routes ─────────────────────────────────────────────────────────────
Route::prefix('sales')->name('sales.')->middleware(['auth', 'role:Sales'])->group(function () {
    Route::get('/dashboard', [Sales\PemesananController::class, 'index'])->name('dashboard');

    Route::get('/pemesanan',                    [Sales\PemesananController::class, 'index'])->name('pemesanan.index');
    Route::post('/pemesanan/cari',              [Sales\PemesananController::class, 'cariJadwal'])->name('pemesanan.cari');
    Route::get('/pemesanan/pilih/{jadwal}',     [Sales\PemesananController::class, 'pilihJadwal'])->name('pemesanan.pilih');
    Route::post('/pemesanan/store',             [Sales\PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/pemesanan/sukses/{pemesanan}', [Sales\PemesananController::class, 'sukses'])->name('pemesanan.sukses');
    Route::get('/pemesanan/kursi-terisi/{jadwal}', [Sales\PemesananController::class, 'getKursiTerisi'])->name('pemesanan.kursiTerisi');
    Route::post('/pemesanan/jadwal-pulang',     [Sales\PemesananController::class, 'getJadwalPulang'])->name('pemesanan.jadwalPulang');

    Route::get('/transaksi', [Sales\TransaksiController::class, 'index'])->name('transaksi.index');
});

// ─── Supir Routes ─────────────────────────────────────────────────────────────
Route::prefix('supir')->name('supir.')->middleware(['auth', 'role:Supir'])->group(function () {
    Route::get('/dashboard', [Supir\JadwalController::class, 'index'])->name('dashboard');
    Route::get('/jadwal',    [Supir\JadwalController::class, 'index'])->name('jadwal.index');
});
