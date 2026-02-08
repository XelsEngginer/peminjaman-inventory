<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void {
    Schema::create('peminjamans', function (Blueprint $table) {
        $table->id('id_pinjam');
        $table->foreignId('id_user')->constrained('users');
        $table->foreignId('id_alat')->constrained('alats', 'id_alat');
        $table->date('tgl_pinjam');
        $table->date('tgl_kembali')->nullable();
        $table->enum('status', ['pending', 'disetujui', 'dikembalikan'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
