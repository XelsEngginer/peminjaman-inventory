<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    
    // INI PENTING! Biar Laravel tau nama tabelnya 'kategori' bukan 'kategoris'
    protected $table = 'kategori'; 
    protected $primaryKey = 'id_kategori'; // Pastikan ini juga benar
    
    protected $guarded = [];

    public function alats()
    {
        return $this->hasMany(Alat::class, 'id_kategori');
    }
}