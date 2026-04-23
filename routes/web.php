<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PinjamController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\PulsaPinjamController;
use App\Http\Controllers\SyncController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');



// Pastikan menggunakan class Controller, bukan function()
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
Route::post('/nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
// Gunakan POST untuk update yang menyertakan File/Foto agar tidak error di beberapa server
Route::post('/nasabah/{id}', [NasabahController::class, 'update'])->name('nasabah.update');
Route::delete('/nasabah/{id}', [NasabahController::class, 'destroy'])->name('nasabah.destroy');
Route::resource('nasabah', NasabahController::class);


Route::resource('pinjam', PinjamController::class);
Route::get('/pinjam', [PinjamController::class, 'index'])->name('pinjam.index');
Route::post('/pinjam', [PinjamController::class, 'store'])->name('pinjam.store');
Route::post('/pinjam/{id}', [PinjamController::class, 'update'])->name('pinjam.update');
Route::delete('/pinjam/{id}', [PinjamController::class, 'destroy'])->name('pinjam.destroy');


Route::resource('angsuran', AngsuranController::class);
Route::get('/angsuran', [AngsuranController::class, 'index'])->name('angsuran.index');
Route::post('/angsuran', [AngsuranController::class, 'store'])->name('angsuran.store');
Route::post('/angsuran/{id}', [AngsuranController::class, 'update'])->name('angsuran.update');
Route::delete('/angsuran/{id}', [AngsuranController::class, 'destroy'])->name('angsuran.destroy');
Route::get('/angsuran/detail/{id}', [AngsuranController::class, 'getDetailNasabah'])->name('angsuran.get-detail-nasabah');
// Hapus prefix 'admin' jika di JS Anda tidak memakainya
// Sesuaikan URI dengan URL yang Anda panggil di browser
Route::get('/angsuran/printstruk/{id}', [App\Http\Controllers\AngsuranController::class, 'printStruk'])->name('angsuran.print');

// Route untuk hapus (SweetAlert)
Route::delete('/angsuran/delete/{id}', [AngsuranController::class, 'destroy'])->name('admin.angsuran.delete');



// Pastikan tidak ada typo pada kata 'pulsa'
Route::get('/pulsa', [PulsaPinjamController::class, 'index'])->name('pulsa.index');
// Route Utama untuk Tampilan dan Lazy Load
// Route CRUD (Create, Update, Delete) via AJAX
Route::post('/pulsa/store', [PulsaPinjamController::class, 'store'])->name('pulsa.store');
Route::put('/pulsa/update/{id}', [PulsaPinjamController::class, 'update'])->name('pulsa.update');
Route::delete('/pulsa/delete/{id}', [PulsaPinjamController::class, 'destroy'])->name('pulsa.destroy');

// Route Tambahan (Opsional) untuk ambil data spesifik nasabah jika dibutuhkan di modal
Route::get('/pulsa/nasabah', [PulsaPinjamController::class, 'getNasabah'])->name('pulsa.nasabah');

Route::get('/pengaturan', [SyncController::class, 'index'])->name('pengaturan.index');
Route::post('/pengaturan/update', [SyncController::class, 'update'])->name('pengaturan.update');
Route::get('/sync/start', [SyncController::class, 'startSync'])->name('sync.start');
