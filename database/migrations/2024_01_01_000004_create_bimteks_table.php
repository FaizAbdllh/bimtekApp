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
            // 1. Identitas Utama & Hak Akses (UUID)
            $table->uuid('id')->primary();
            $table->foreignUuid('pic_user_id')->nullable()->constrained('users')->onDelete('set null');

            // 2. Data Perencanaan Kegiatan (Eks Tabel Pengajuans)
            $table->string('judul_rencana');
            $table->string('tempat_kegiatan_rencana')->nullable();
            $table->string('sumber_pembiayaan')->nullable();
            $table->date('tanggal_mulai_rencana')->nullable();
            $table->date('tanggal_selesai_rencana')->nullable();
            $table->text('deskripsi_rencana')->nullable();
            $table->integer('jumlah_peserta')->nullable()->comment('Estimasi jumlah peserta');
            $table->enum('jenis_kegiatan', ['internal', 'eksternal'])->default('eksternal');

            // 3. Data Aktual Pelaksanaan (Eks Tabel Bimteks)
            $table->string('judul_final')->nullable();
            $table->string('lokasi_aktual')->nullable();
            $table->date('tanggal_mulai_aktual')->nullable();
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->enum('mode_pelaksanaan', ['offline', 'online', 'hybrid'])->default('offline');
            $table->string('virtual_meeting_url', 500)->nullable(); 
            $table->string('invite_code', 64)->nullable()->unique(); 
            $table->decimal('anggaran_disetujui', 15, 2)->nullable();
            $table->text('deskripsi_jadwal')->nullable();
            $table->json('daftar_pemateri')->nullable(); 

            // 4. Manajemen Dokumen Surat Undangan (Single Workflow - Diunggah oleh Panitia)
            $table->string('file_surat_undangan_path')->nullable();
            $table->foreignUuid('file_surat_undangan_uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('file_surat_undangan_uploaded_at')->nullable();

            // 5. Aturan Kelulusan & Verifikasi (Sesuai Perancangan Baru)
            $table->boolean('butuh_verifikasi_dokumen')->default(false); 
            $table->boolean('has_tugas')->default(true);
            $table->boolean('has_sertifikat')->default(true);
            $table->integer('syarat_kehadiran_persen')->default(80);
            $table->integer('syarat_tugas_persen')->default(70);
            $table->boolean('syarat_tugas_wajib')->default(false);

            // 6. Alur Kerja & Status Tunggal (State Machine BPMN)
            $table->enum('status', [
                'draft_pic',
                'diajukan',
                'disetujui_kepala',
                'disetujui_ppk',
                'disetujui_final',
                'persiapan',
                'berlangsung',
                'selesai',
                'dibatalkan',
                'ditolak',
                'perlu_revisi'
            ])->default('draft_pic');

            // 7. Catatan Log & Validasi Pejabat/Rumah Tangga (Update Kelompok 3)
            $table->text('catatan_kepala')->nullable();
            $table->timestamp('kepala_approved_at')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->timestamp('ppk_approved_at')->nullable();
            $table->enum('status_rt', ['belum_dipenuhi', 'sebagian_dipenuhi', 'telah_dipenuhi'])->default('belum_dipenuhi');
            $table->text('catatan_rt')->nullable();

            $table->timestamps();

            // --- TAMBAHAN INDEX PERFORMA (Dari file add_performance_indexes) ---
            $table->index('status');
            $table->index('status_rt');
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