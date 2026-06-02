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
        // Convert existing ENUM values to readable format before changing column type
        DB::statement("UPDATE dokumen_persyaratan_peserta SET jenis_dokumen = 'Surat Tugas' WHERE jenis_dokumen = 'surat_tugas'");
        DB::statement("UPDATE dokumen_persyaratan_peserta SET jenis_dokumen = 'SPPD' WHERE jenis_dokumen = 'sppd'");

        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite: recreate table with new column type
            Schema::table('dokumen_persyaratan_peserta', function (Blueprint $table) {
                $table->string('jenis_dokumen', 100)->change();
            });
        } else {
            // MySQL: alter column type from ENUM to VARCHAR
            DB::statement('ALTER TABLE dokumen_persyaratan_peserta MODIFY COLUMN jenis_dokumen VARCHAR(100) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to snake_case before reverting to ENUM
        DB::statement("UPDATE dokumen_persyaratan_peserta SET jenis_dokumen = 'surat_tugas' WHERE jenis_dokumen = 'Surat Tugas'");
        DB::statement("UPDATE dokumen_persyaratan_peserta SET jenis_dokumen = 'sppd' WHERE jenis_dokumen = 'SPPD'");

        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('dokumen_persyaratan_peserta', function (Blueprint $table) {
                $table->string('jenis_dokumen')->change();
            });
        } else {
            // Note: This will truncate any new document types to empty string
            DB::statement("ALTER TABLE dokumen_persyaratan_peserta MODIFY COLUMN jenis_dokumen ENUM('surat_tugas', 'sppd') NOT NULL");
        }
    }
};
