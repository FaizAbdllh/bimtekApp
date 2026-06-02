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
        if (Schema::hasColumn('absensi_pesertas', 'bukti_hadir_online_path')) {
            return;
        }

        Schema::table('absensi_pesertas', function (Blueprint $table) {
            $table->string('bukti_hadir_online_path')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('absensi_pesertas', 'bukti_hadir_online_path')) {
            return;
        }

        Schema::table('absensi_pesertas', function (Blueprint $table) {
            $table->dropColumn('bukti_hadir_online_path');
        });
    }
};
