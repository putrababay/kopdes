<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NasabahController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');



// Pastikan menggunakan class Controller, bukan function()
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
Route::get('/nasabah/data', [NasabahController::class, 'getData'])->name('nasabah.data');