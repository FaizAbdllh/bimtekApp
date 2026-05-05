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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul_rencana');
            $table->string('tempat_kegiatan')->nullable();
            $table->string('sumber_pembiayaan')->nullable();
            $table->date('tanggal_mulai_rencana')->nullable();
            $table->date('tanggal_selesai_rencana')->nullable();
            $table->text('deskripsi_rencana')->nullable();
            $table->enum('jenis_kegiatan', ['internal', 'eksternal'])->default('eksternal');
            $table->enum('status_pengajuan', [
                'diajukan',
                'disetujui_kepala',
                'disetujui_ppk',
                'disetujui_final',
                'ditolak',
                'perlu_revisi'
            ])->notNull()->default('diajukan');
            $table->text('catatan_kepala')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->enum('status_rt', ['belum_dipenuhi', 'telah_dipenuhi'])->notNull()->default('belum_dipenuhi');
            $table->text('catatan_rt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
