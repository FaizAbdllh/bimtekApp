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
        if (Schema::hasColumn('bimteks', 'virtual_meeting_url')) {
            return;
        }

        Schema::table('bimteks', function (Blueprint $table) {
            $table->string('virtual_meeting_url', 500)
                ->nullable()
                ->after('lokasi_aktual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('bimteks', 'virtual_meeting_url')) {
            return;
        }

        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropColumn('virtual_meeting_url');
        });
    }
};