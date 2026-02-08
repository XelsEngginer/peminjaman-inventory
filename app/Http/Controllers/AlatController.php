<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class AlatController extends Controller
{
    /**
     * DASHBOARD & MAIN VIEW ENGINE
     */
    public function index(Request $request) {
        $data['menu'] = $request->query('menu', 'dashboard');
        
        // Statistik Ringkasan
        $data['jml_user'] = User::count();
        $data['jml_alat'] = Alat::count();
        $data['jml_pinjam'] = Peminjaman::where('status', 'pending')->count();
        
        // Data Pendukung
        $data['kategoris'] = Kategori::all();
        $data['users'] = User::all(); 

        // Query Alat dengan Search
        $query = Alat::with('kategori');
        if ($request->has('search')) {
            $query->where('nama_alat', 'LIKE', '%' . $request->search . '%');
        }
        
        $data['alats'] = $query->get(); 
        $data['alats_with_kategori'] = $query->get(); 
        $data['all_pinjam'] = Peminjaman::with(['user', 'alat'])->orderBy('created_at', 'desc')->get();

        // Data Edit (Jika ada)
        if($request->has('edit_alat')) { 
            $data['ea'] = Alat::find($request->edit_alat); 
        }
        
        return view('alat.index', $data);
    }

    /**
     * MANAJEMEN DATA BARANG (ADMIN ONLY)
     */
    public function store(Request $request) {
        if(Auth::user()->role !== 'admin') { 
            return redirect()->back()->with('error', 'Akses Ditolak!'); 
        }

        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'stok' => 'required|numeric|min:0'
        ]);

        Alat::updateOrCreate(
            ['id_alat' => $request->id],
            ['nama_alat' => $request->nama, 'id_kategori' => $request->kategori, 'stok' => $request->stok]
        );

        return redirect()->route('alat.index', ['menu'=>'alat'])->with('success','Data Barang Berhasil Disimpan!');
    }

    public function destroy($id) {
        if(Auth::user()->role !== 'admin') { 
            return redirect()->back()->with('error', 'Akses ditolak!'); 
        }

        try {
            DB::transaction(function() use ($id) {
                // Hapus anak (peminjaman) dulu baru bapak (alat) agar tidak error SQLSTATE[23000]
                Peminjaman::where('id_alat', $id)->delete();
                Alat::destroy($id);
            });
            return back()->with('success','Data barang dan seluruh riwayatnya berhasil dihapus!'); 
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data barang!');
        }
    }

    /**
     * MANAJEMEN USER (ADMIN ONLY)
     */
    public function storeUser(Request $request) {
        if(Auth::user()->role !== 'admin') { 
            return redirect()->back()->with('error', 'Akses ditolak!'); 
        }
        $request->validate(['username'=>'required','password'=>'required','role'=>'required']);
        
        User::create([
            'username' => $request->username, 
            'password' => Hash::make($request->password), 
            'role' => $request->role
        ]);
        
        return redirect()->route('alat.index',['menu'=>'user'])->with('success','User baru berhasil didaftarkan!');
    }

    public function destroyUser($id) { 
        if(Auth::user()->role !== 'admin') { 
            return redirect()->back()->with('error', 'Akses ditolak!'); 
        }
        User::destroy($id); 
        return redirect()->route('alat.index',['menu'=>'user'])->with('success','Akun user telah dihapus!'); 
    }

    /**
     * LOGIKA TRANSAKSI PEMINJAMAN (SISI PEMINJAM)
     * Menggunakan lockForUpdate untuk mencegah Race Condition (Stok Minus)
     */
    public function ajukanPinjam(Request $request) {
        $request->validate(['id_alat' => 'required', 'jumlah_pinjam' => 'required|numeric|min:1']);

        return DB::transaction(function() use ($request) {
            // Kunci baris data alat agar tidak bisa diproses script lain sampai transaksi selesai
            $alat = Alat::where('id_alat', $request->id_alat)->lockForUpdate()->firstOrFail();

            if($alat->stok < $request->jumlah_pinjam) {
                return redirect()->back()->with('error', 'Maaf, stok baru saja habis atau tidak mencukupi!');
            }

            for($i = 0; $i < $request->jumlah_pinjam; $i++) {
                Peminjaman::create([
                    'id_user' => Auth::id(), 
                    'id_alat' => $request->id_alat, 
                    'tgl_pinjam' => now(), 
                    'status' => 'pending'
                ]);
            }
            return redirect()->back()->with('success', 'Permintaan peminjaman sedang menunggu konfirmasi petugas.');
        });
    }

    /**
     * LOGIKA PERSETUJUAN (SISI PETUGAS/ADMIN)
     */
    public function setujuiPinjam($id) {
        if(Auth::user()->role == 'peminjam') { return redirect()->back()->with('error', 'Akses dilarang!'); } 
        
        return DB::transaction(function() use ($id) {
            $pinjam = Peminjaman::findOrFail($id);
            $alat = Alat::where('id_alat', $pinjam->id_alat)->lockForUpdate()->firstOrFail();

            if ($alat->stok <= 0) {
                return redirect()->back()->with('error', 'Gagal! Stok barang ini sudah kosong di gudang.');
            }

            $pinjam->update(['status' => 'disetujui']);
            $alat->decrement('stok');

            return redirect()->back()->with('success', 'Peminjaman disetujui, stok berhasil dikurangi.');
        });
    }

    /**
     * FITUR BARU: TOLAK PEMINJAMAN
     */
    public function tolakPinjam($id) {
        if(Auth::user()->role == 'peminjam') { return redirect()->back()->with('error', 'Akses dilarang!'); } 
        
        $pinjam = Peminjaman::findOrFail($id);
        
        if ($pinjam->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permintaan baru yang bisa ditolak.');
        }

        $pinjam->update(['status' => 'ditolak']);
        
        return redirect()->back()->with('success', 'Permintaan peminjaman telah ditolak.');
    }

    /**
     * LOGIKA PENGEMBALIAN
     */
    public function kembalikanAlat($id) {
        if(Auth::user()->role == 'peminjam') { return redirect()->back()->with('error', 'Silakan hubungi petugas!'); } 
        
        $pinjam = Peminjaman::findOrFail($id);
        if ($pinjam->status !== 'disetujui') { return redirect()->back()->with('error', 'Data tidak valid.'); }

        return DB::transaction(function() use ($pinjam) {
            $alat = Alat::where('id_alat', $pinjam->id_alat)->lockForUpdate()->firstOrFail();
            
            $pinjam->update([
                'status' => 'dikembalikan', 
                'tgl_kembali' => now()
            ]);
            
            $alat->increment('stok');
            return redirect()->back()->with('success', 'Barang berhasil dikembalikan, stok bertambah.');
        });
    }

    /**
     * LAPORAN
     */
    public function cetakLaporan() {
        if(Auth::user()->role == 'peminjam') { return redirect()->back()->with('error', 'Akses ditolak!'); } 
        $data['laporan'] = Peminjaman::with(['user', 'alat'])->orderBy('tgl_pinjam', 'desc')->get();
        return view('alat.cetak', $data);
    }
}