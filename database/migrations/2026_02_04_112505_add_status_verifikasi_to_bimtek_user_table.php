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
        Schema::table('bimtek_user', function (Blueprint $table) {
            // For SQLite (testing), use string column
            if (DB::connection()->getDriverName() === 'sqlite') {
                $table->string('status_verifikasi')->nullable()->after('peran_kontekstual');
            } else {
                // For MySQL, use ENUM - nullable, no default
                DB::statement("ALTER TABLE bimtek_user ADD COLUMN status_verifikasi ENUM('invited', 'pending', 'verified', 'rejected') NULL AFTER peran_kontekstual");
            }
            $table->timestamp('notified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimtek_user', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'notified_at']);
        });
    }
};
