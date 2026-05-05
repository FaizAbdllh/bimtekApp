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
        // Tambah kolom is_dipenuhi di fasilitas_logistiks
        Schema::table('fasilitas_logistiks', function (Blueprint $table) {
            $table->boolean('is_dipenuhi')->default(false)->after('satuan');
        });

        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Tambah status 'sebagian_dipenuhi' di pengajuans jika belum ada
            // Menggunakan raw query karena ALTER ENUM di MySQL
            DB::statement("ALTER TABLE pengajuans MODIFY COLUMN status_rt ENUM('belum_dipenuhi', 'sebagian_dipenuhi', 'telah_dipenuhi') NOT NULL DEFAULT 'belum_dipenuhi'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasilitas_logistiks', function (Blueprint $table) {
            $table->dropColumn('is_dipenuhi');
        });

        // Skip for SQLite (testing environment)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pengajuans MODIFY COLUMN status_rt ENUM('belum_dipenuhi', 'telah_dipenuhi') NOT NULL DEFAULT 'belum_dipenuhi'");
        }
    }
};
