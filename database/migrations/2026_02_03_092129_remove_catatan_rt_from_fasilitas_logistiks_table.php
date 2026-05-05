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
            $table->dropColumn('catatan_rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasilitas_logistiks', function (Blueprint $table) {
            $table->text('catatan_rt')->nullable();
        });
    }
};
