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
        Schema::create('bimteks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengajuan_id')->unique()->constrained('pengajuans')->onDelete('cascade');
            $table->string('judul_final');
            $table->enum('mode_pelaksanaan', ['offline', 'online', 'hybrid'])->default('offline');
            $table->date('tanggal_mulai_aktual')->nullable();
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->string('lokasi_aktual')->nullable();
            $table->string('virtual_meeting_url', 500)->nullable();
            $table->decimal('anggaran_disetujui', 15, 2)->nullable();
            $table->text('deskripsi_jadwal')->nullable()->comment('Menggantikan tabel jadwal');
            $table->string('file_surat_undangan_path')->nullable()->comment('Menyimpan path file surat undangan');
            $table->enum('status_pelaksanaan', ['persiapan', 'berlangsung', 'selesai'])->notNull()->default('persiapan');
            // Syarat sertifikat per bimtek
            $table->integer('syarat_kehadiran_persen')->default(80)->comment('Persentase kehadiran minimal (0-100)');
            $table->integer('syarat_tugas_persen')->default(70)->comment('Persentase nilai tugas minimal (0-100)');
            $table->boolean('syarat_tugas_wajib')->default(false)->comment('Apakah tugas wajib untuk sertifikat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimteks');
    }
};
