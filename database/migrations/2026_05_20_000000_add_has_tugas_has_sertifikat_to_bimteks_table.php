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
        Schema::table('bimteks', function (Blueprint $table) {
            $table->boolean('has_tugas')->default(true)->after('daftar_pemateri');
            $table->boolean('has_sertifikat')->default(true)->after('has_tugas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropColumn(['has_tugas', 'has_sertifikat']);
        });
    }
};
