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
        Schema::table('pengajuans', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuans', 'kebutuhan_ringkas')) {
                $table->dropColumn('kebutuhan_ringkas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuans', 'kebutuhan_ringkas')) {
                $table->text('kebutuhan_ringkas')->nullable()->after('jumlah_peserta')->comment('Ringkasan kebutuhan / telaah staf (short list)');
            }
        });
    }
};