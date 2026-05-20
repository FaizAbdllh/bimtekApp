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
        Schema::create('absensi_pesertas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sesi_absensi_id')->constrained('sesi_absensis')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bukti_hadir_online_path')->nullable();
            $table->timestamps();

            // Satu user hanya bisa absen sekali per sesi
            $table->unique(['sesi_absensi_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pesertas');
    }
};
