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
        // 1. Membuat Tabel Fasilitas Logistik yang Sudah Sempurna
        Schema::create('fasilitas_logistiks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // JANGKAR BARU: Menembak langsung ke UUID Bimtek, bukan pengajuan lagi
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            
            $table->string('nama_fasilitas');
            $table->integer('jumlah')->default(1);
            
            // Konsolidasi dari file penambal satuan tahun 2025
            $table->string('satuan')->nullable()->comment('Contoh: Kotak, Rim, Dos, Orang');
            
            // Konsolidasi dari file penambal Kelompok 3 (Rumah Tangga)
            $table->boolean('is_dipenuhi')->default(false);
            
            $table->enum('status', ['diminta', 'tersedia', 'tidak_tersedia'])->default('diminta');
            
            $table->timestamps();
        });

        // 2. Menitipkan kolom catatan_logistik ke tabel bimteks (Bukan pengajuans lagi)
        Schema::table('bimteks', function (Blueprint $table) {
            $table->text('catatan_logistik')->nullable()->after('catatan_rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop kolom di tabel bimteks terlebih dahulu
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropColumn('catatan_logistik');
        });

        Schema::dropIfExists('fasilitas_logistiks');
    }
};