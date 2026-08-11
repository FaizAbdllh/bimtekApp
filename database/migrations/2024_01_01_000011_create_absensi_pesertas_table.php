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
        Schema::create('absensi_pesertas', function (Blueprint $table) {
            // 1. Relasi ke Sesi Absensi
            $table->foreignUuid('sesi_absensi_id')->constrained('sesi_absensis')->onDelete('cascade');

            // 2. COMPOSITE FOREIGN KEY
            // Mengikat langsung ke tabel pendaftaran baru (bimtek_pesertas)
            $table->uuid('bimtek_id');
            $table->uuid('user_id');
            $table->foreign(['bimtek_id', 'user_id'])
                  ->references(['bimtek_id', 'user_id'])
                  ->on('bimtek_pesertas')
                  ->onDelete('cascade');

            // KONSOLIDASI PATCH 2026: Kolom bukti presensi daring (diletakkan setelah user_id sesuai patch)
            $table->string('bukti_hadir_online_path')->nullable();

            // 3. Atribut Status Kehadiran Utama
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alfa'])->default('alfa');
            $table->timestamp('waktu_presensi')->nullable();

            $table->timestamps();

            // 4. COMPOSITE PRIMARY KEY
            // Satu peserta hanya boleh memiliki satu baris status kehadiran per satu sesi absen
            $table->primary(['sesi_absensi_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pesertas');
    }
};