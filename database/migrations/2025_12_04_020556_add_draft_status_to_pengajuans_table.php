<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Modify enum to include 'draft' as the first option
        DB::statement("ALTER TABLE pengajuans MODIFY COLUMN status_pengajuan ENUM('draft', 'diajukan', 'disetujui_kepala', 'disetujui_ppk', 'disetujui_final', 'ditolak', 'perlu_revisi') NOT NULL DEFAULT 'diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Revert to original enum without 'draft'
        DB::statement("ALTER TABLE pengajuans MODIFY COLUMN status_pengajuan ENUM('diajukan', 'disetujui_kepala', 'disetujui_ppk', 'disetujui_final', 'ditolak', 'perlu_revisi') NOT NULL DEFAULT 'diajukan'");
    }
};
