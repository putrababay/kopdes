<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PinjamController;


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
