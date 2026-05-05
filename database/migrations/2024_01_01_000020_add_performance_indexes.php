<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan index untuk meningkatkan performa query
     */
    public function up(): void
    {
        // Index untuk pengajuans
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->index('status_pengajuan');
            $table->index('status_rt');
        });

        // Index untuk bimteks
        Schema::table('bimteks', function (Blueprint $table) {
            $table->index('status_pelaksanaan');
        });

        // Index untuk log_sistems
        Schema::table('log_sistems', function (Blueprint $table) {
            $table->index('level');
            $table->index('created_at');
        });

        // Index untuk kebutuhan_anggarans
        Schema::table('kebutuhan_anggarans', function (Blueprint $table) {
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropIndex(['status_pengajuan']);
            $table->dropIndex(['status_rt']);
        });

        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropIndex(['status_pelaksanaan']);
        });

        Schema::table('log_sistems', function (Blueprint $table) {
            $table->dropIndex(['level']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('kebutuhan_anggarans', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
        });
    }
};
