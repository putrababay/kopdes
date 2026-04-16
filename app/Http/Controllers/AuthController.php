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

        // Mencari user sesuai logika native: username, password (plain), dan level ADMIN
        $user = Nasabah::where('username', $request->username)
                        ->where('password', $request->password)
                        ->where('level', 'ADMIN')
                        ->first();

        if ($user) {
            // Set session mirip dengan native $_SESSION
            Session::put('nik', $user->nik);
            Session::put('nama', $user->nama);
            Session::put('level', $user->level);

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Login gagal, harap periksa username dan password Anda');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}