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
        Schema::create('sesi_absensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            $table->string('nama_sesi');
            
            // Status tunggal penentu aktif/tidaknya absensi
            $table->enum('status', ['terbuka', 'ditutup'])->default('ditutup');
            
            // Payload string QR Code (Hanya menyimpan data string QR-nya saja)
            $table->text('qr_code')->nullable();
            
            // FK ke users (PIC/Panitia yang membuka sesi)
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Index performa
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_absensis');
    }
};