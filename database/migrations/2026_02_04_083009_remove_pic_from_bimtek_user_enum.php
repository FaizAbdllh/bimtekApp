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

        // Remove 'pic' from enum, leaving only 'panitia' and 'peserta'
        DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('panitia', 'peserta') NOT NULL");
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

        // Restore 'pic' to enum
        DB::statement("ALTER TABLE bimtek_user MODIFY peran_kontekstual ENUM('pic', 'panitia', 'peserta') NOT NULL");
    }
};
