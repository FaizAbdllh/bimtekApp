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
        Schema::create('dokumen_persyaratan_peserta', function (Blueprint $table) {
            // 1. Relasi Induk ke Komponen Syarat Dokumen (Fase 3)
            $table->foreignUuid('syarat_dokumen_id')->constrained('syarat_dokumens')->onDelete('cascade');

            // 2. COMPOSITE FOREIGN KEY (Menembak langsung ke tabel pendaftaran peserta)
            // Memastikan berkas ini hanya diunggah oleh user yang berstatus peserta resmi di Bimtek tersebut
            $table->uuid('bimtek_id');
            $table->uuid('user_id');
            $table->foreign(['bimtek_id', 'user_id'])
                  ->references(['bimtek_id', 'user_id'])
                  ->on('bimtek_pesertas')
                  ->onDelete('cascade');

            // 3. Atribut File & Transaksi Upload
            $table->string('file_path');
            $table->string('file_name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('uploaded_at')->useCurrent();

            // 4. Atribut Verifikasi & Validasi Dokumen oleh Panitia
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->onDelete('set null')->comment('Panitia yang memverifikasi');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->timestamps();

            // 5. COMPOSITE PRIMARY KEY
            // Mengunci agar satu peserta hanya bisa mengunggah satu berkas per satu item persyaratan
            $table->primary(['syarat_dokumen_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_persyaratan_peserta');
    }
};