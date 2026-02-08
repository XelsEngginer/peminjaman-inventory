<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key otomatis
            $table->string('username')->unique(); // Untuk login sesuai form kamu
            $table->string('password'); // Password yang di-hash
            
            // Kolom ROLE untuk membedakan hak akses sesuai tabel soal UKK
            // Admin: CRUD User & Alat, Petugas: Setujui Pinjam, Peminjam: Ajukan Pinjam
            $table->enum('role', ['admin', 'petugas', 'peminjam'])->default('peminjam');
            
            $table->rememberToken();
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};