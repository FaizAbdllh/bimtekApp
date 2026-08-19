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
        Schema::create('sertifikats', function (Blueprint $table) {
            // 1. COMPOSITE FOREIGN KEY (Mengunci ke pendaftaran peserta)
            // Menjamin sertifikat hanya terbit untuk user yang terdaftar resmi di Bimtek tersebut
            $table->uuid('bimtek_id');
            $table->uuid('user_id');
            $table->foreign(['bimtek_id', 'user_id'])
                  ->references(['bimtek_id', 'user_id'])
                  ->on('bimtek_pesertas')
                  ->onDelete('cascade');

            // 2. Atribut Sertifikat (Bersih dari bug ->notNull() dan template_id)
            $table->string('nomor_sertifikat')->unique()->comment('Kode unik sertifikat resmi BBPMP');
            $table->date('tanggal_terbit');
            $table->string('file_path')->comment('Path file PDF sertifikat yang sudah di-generate');
            
            $table->timestamps();

            // 3. COMPOSITE PRIMARY KEY
            // Mengunci agar satu peserta hanya bisa memiliki satu catatan kelulusan/sertifikat per kegiatan
            $table->primary(['bimtek_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};