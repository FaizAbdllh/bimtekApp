<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen_persyaratan_peserta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');

            // For SQLite compatibility
            if (DB::connection()->getDriverName() === 'sqlite') {
                $table->string('jenis_dokumen'); // surat_tugas, sppd
                $table->string('status')->default('pending'); // pending, approved, rejected
            }

            $table->string('file_path');
            $table->string('file_name');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['bimtek_id', 'user_id']);
        });

        // Add ENUMs using raw SQL for MySQL
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dokumen_persyaratan_peserta ADD COLUMN jenis_dokumen ENUM('surat_tugas', 'sppd') NOT NULL AFTER user_id");
            DB::statement("ALTER TABLE dokumen_persyaratan_peserta ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' NOT NULL AFTER file_name");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_persyaratan_peserta');
    }
};
