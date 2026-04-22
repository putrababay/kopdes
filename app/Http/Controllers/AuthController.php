<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Cek jika sudah login
        if (Session::has('nik')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari user berdasarkan username dan level saja
        $user = Nasabah::where('username', $request->username)
            ->where('level', 'ADMIN')
            ->first();

        // Verifikasi User ada DAN password cocok
        // Jika password di database masih plain, gunakan: if ($user && $user->password == $request->password)
        // Jika sudah di-hash (disarankan), gunakan Hash::check:
        if ($user && $user->password == $request->password) {

            // Set session
            Session::put('nik', $user->nik);
            Session::put('nama', $user->nama);
            Session::put('level', $user->level);

            // Regenerate session ID untuk mencegah Session Fixation (Keamanan tambahan)
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        // Jika gagal, kembali dengan input username agar user tidak perlu ngetik ulang
        return back()->with('error', 'Login gagal, harap periksa username dan password Anda')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        // Hapus session spesifik atau semuanya
        Session::flush();

        // Invalidasi session agar tidak bisa dipakai lagi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
