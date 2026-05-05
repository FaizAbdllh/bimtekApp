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
        Schema::table('fasilitas_logistiks', function (Blueprint $table) {
            $table->string('satuan')->default('Unit')->after('jumlah');
        });

        // Tambah kolom status_draft ke pengajuans untuk fitur draft
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->boolean('is_draft')->default(false)->after('status_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasilitas_logistiks', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });

        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn('is_draft');
        });
    }
};
