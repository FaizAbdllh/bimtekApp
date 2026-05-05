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
        Schema::table('bimtek_user', function (Blueprint $table) {
            $table->string('fungsi_panitia', 100)->nullable()->after('peran_kontekstual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimtek_user', function (Blueprint $table) {
            $table->dropColumn('fungsi_panitia');
        });
    }
};
