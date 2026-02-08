<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    // Pengganti Logika Tampilan (switch menu)
    public function index(Request $request)
    {
        $menu = $request->query('menu', 'dashboard');
        
        // Data untuk Dashboard Statistik
        $jml_user = \App\Models\User::count();
        $jml_alat = Alat::count();
        $jml_pinjam = Peminjaman::count();

        // Ambil data berdasarkan menu
        $alats = Alat::with('kategori')->get();
        $kategoris = Kategori::all();
        $pending_pinjam = Peminjaman::with(['user', 'alat'])->where('status', 'pending')->get();
        $active_pinjam = Peminjaman::with('user')->where('status', 'disetujui')->get();

        return view('peminjaman.index', compact('menu', 'jml_user', 'jml_alat', 'jml_pinjam', 'alats', 'kategoris', 'pending_pinjam', 'active_pinjam'));
    }

    // Pengganti Logika Simpan/Update Alat
    public function simpanAlat(Request $request)
    {
        Alat::updateOrCreate(
            ['id_alat' => $request->id],
            ['nama_alat' => $request->nama, 'id_kategori' => $request->kategori, 'stok' => $request->stok]
        );
        return redirect()->back()->with('success', 'Data Berhasil Disimpan');
    }

    // Pengganti Logika Hapus Alat
    public function hapusAlat($id)
    {
        Alat::where('id_alat', $id)->delete();
        return redirect()->back();
    }
}
