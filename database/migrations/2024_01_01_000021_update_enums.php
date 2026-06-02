<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan 'pemateri' ke enum peran_kontekstual dan 'dibatalkan' ke status_pelaksanaan
     */
    public function up(): void
    {
        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Modify bimtek_user enum to add 'pemateri'
        DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('pic', 'panitia', 'peserta', 'pemateri') NOT NULL");

        // Modify bimteks enum to add 'dibatalkan'
        DB::statement("ALTER TABLE bimteks MODIFY status_pelaksanaan ENUM('persiapan', 'berlangsung', 'selesai', 'dibatalkan') DEFAULT 'persiapan'");
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

        // Remove 'pemateri' from enum (requires no data using that value)
        DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('pic', 'panitia', 'peserta') NOT NULL");

        // Remove 'dibatalkan' from enum
        DB::statement("ALTER TABLE bimteks MODIFY status_pelaksanaan ENUM('persiapan', 'berlangsung', 'selesai') DEFAULT 'persiapan'");
    }
};
