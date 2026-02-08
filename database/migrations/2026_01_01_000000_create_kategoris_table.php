<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel kategori sesuai soal UKK[cite: 30, 51].
     */
public function up(): void
{
    Schema::create('kategori', function (Blueprint $table) {
        $table->id('id_kategori'); // Harus 'id' atau 'id_kategori' dan tipe BigInt
        $table->string('nama_kategori');
        $table->timestamps();
    });
}

    /**
     * Membatalkan migrasi (Hapus tabel)[cite: 52].
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};