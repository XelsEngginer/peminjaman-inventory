<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    // Nama tabel harus persis seperti di phpMyAdmin
    protected $table = 'alats'; 
    
    // Primary key wajib didefinisikan karena bukan 'id'
    protected $primaryKey = 'id_alat'; 

    // Kolom yang boleh diisi (Mass Assignment) 
    protected $fillable = [
        'nama_alat', 
        'id_kategori', 
        'stok'
    ];

    /**
     * RELASI: Setiap alat memiliki satu kategori (BelongsTo) [cite: 51, 6]
     * Ini yang membuat nama "Elektronik/Multimedia" bisa muncul di tabel
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}