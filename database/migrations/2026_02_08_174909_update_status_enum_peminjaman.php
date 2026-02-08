<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // WAJIB tambahkan ini agar DB::statement jalan

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jurus sakti untuk menambah 'ditolak' ke dalam daftar ENUM status
        DB::statement("ALTER TABLE peminjamans MODIFY COLUMN status ENUM('pending', 'disetujui', 'dikembalikan', 'ditolak') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan ke pengaturan awal jika migration di-rollback
        DB::statement("ALTER TABLE peminjamans MODIFY COLUMN status ENUM('pending', 'disetujui', 'dikembalikan') DEFAULT 'pending'");
    }
};