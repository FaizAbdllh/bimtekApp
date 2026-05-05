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
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->text('qr_code')->nullable()->after('status');
            $table->timestamp('qr_generated_at')->nullable()->after('qr_code');
            $table->timestamp('qr_expires_at')->nullable()->after('qr_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_absensis', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'qr_generated_at', 'qr_expires_at']);
        });
    }
};
