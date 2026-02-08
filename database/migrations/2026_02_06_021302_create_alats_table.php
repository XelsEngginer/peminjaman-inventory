<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel alat (UKK Paket 1).
     */
    public function up(): void
    {
        Schema::create('alats', function (Blueprint $table) {
            $table->id('id_alat'); // Primary Key sesuai kebutuhan UKK 
            $table->string('nama_alat'); // Menampung nama alat 
            
            // Perbaikan Relasi: Gunakan tipe data yang sama persis dengan id_kategori
            $table->unsignedBigInteger('id_kategori'); 
            
            // Definisi Foreign Key manual agar lebih stabil saat migrate:fresh
            $table->foreign('id_kategori')
                  ->references('id_kategori')
                  ->on('kategori')
                  ->onDelete('cascade'); // Operasi relasional sesuai soal 
            
            $table->integer('stok'); // Fitur stok sesuai instruksi soal 
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};