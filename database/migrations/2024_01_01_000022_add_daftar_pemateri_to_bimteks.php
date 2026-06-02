<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah field daftar_pemateri di tabel bimteks untuk dokumentasi/laporan
     * Menghapus enum 'pemateri' dari bimtek_user (pemateri bukan user sistem)
     */
    public function up(): void
    {
        // Tambah field daftar_pemateri di tabel bimteks
        Schema::table('bimteks', function (Blueprint $table) {
            $table->json('daftar_pemateri')->nullable()->after('deskripsi_jadwal');
        });

        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Hapus 'pemateri' dari enum peran_kontekstual
            DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('pic', 'panitia', 'peserta') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropColumn('daftar_pemateri');
        });

        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Kembalikan enum dengan 'pemateri'
            DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('pic', 'panitia', 'peserta', 'pemateri') NOT NULL");
        }
    }
};
