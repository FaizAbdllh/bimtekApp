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
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            // 1. Relasi Induk ke Tabel Tugas
            $table->foreignUuid('tugas_id')->constrained('tugas')->onDelete('cascade');

            // 2. COMPOSITE FOREIGN KEY (Mengunci ke tabel pendaftaran peserta)
            // Menjamin user yang mengumpulkan tugas adalah peserta resmi di Bimtek pemilik tugas tersebut
            $table->uuid('bimtek_id');
            $table->uuid('user_id');
            $table->foreign(['bimtek_id', 'user_id'])
                  ->references(['bimtek_id', 'user_id'])
                  ->on('bimtek_pesertas')
                  ->onDelete('cascade');

            // 3. Atribut Transaksi Jawaban & Penilaian Akademik
            $table->string('file_jawaban_path')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('feedback')->nullable();
            
            // Relasi ke tabel users untuk mencatat aktor panitia/PIC yang memberikan nilai
            $table->foreignUuid('user_id_penilai')->nullable()->constrained('users')->onDelete('set null')->comment('PIC/Panitia yang menilai');
            
            $table->timestamps();

            // 4. COMPOSITE PRIMARY KEY
            // Satu peserta hanya diperbolehkan mengirimkan satu berkas jawaban per tugas
            $table->primary(['tugas_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};