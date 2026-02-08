<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    
    // Pastikan nama tabel benar (jamak)
    protected $table = 'peminjamans'; 
    protected $primaryKey = 'id_pinjam';
    
    protected $guarded = []; // Biar bisa simpan semua kolom

    // Relasi ke User (Peminjam)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke Alat (Barang)
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'id_alat');
    }
}