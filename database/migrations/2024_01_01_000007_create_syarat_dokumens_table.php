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
        Schema::create('syarat_dokumens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Menembak ke tabel master Bimtek
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            
            $table->string('nama_dokumen')->comment('Contoh: Surat Tugas, KTP, Pakta Integritas');
            $table->text('deskripsi_syarat')->nullable()->comment('Keterangan tambahan atau link template dokumen jika ada');
            $table->boolean('is_wajib')->default(true)->comment('Apakah dokumen ini wajib atau opsional');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syarat_dokumens');
    }
};