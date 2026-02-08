<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller 
{
    // Menampilkan halaman login
    public function login() 
    { 
        return view('login'); 
    }

    // Memproses data login
    public function prosesLogin(Request $request) 
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // 2. Coba autentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil, buat ulang session agar aman
            $request->session()->regenerate();
            
            // Arahkan ke dashboard atau halaman yang dituju sebelumnya
            return redirect()->intended('/dashboard');
        }

        // 3. JIKA GAGAL: Kembalikan dengan pesan error dan input lama (username)
        // .withInput() sangat penting agar username tidak terhapus saat password salah
        return back()
            ->with('error', 'Login Gagal: Username atau Password salah!')
            ->withInput($request->only('username')); 
    }

    // Menangani proses keluar sistem
    public function logout(Request $request) 
    {
        Auth::logout();

        // Hancurkan session lama agar aman
        $request->session()->invalidate();

        // Buat ulang token CSRF baru
        $request->session()->regenerateToken();

        return redirect('/');
    }
}