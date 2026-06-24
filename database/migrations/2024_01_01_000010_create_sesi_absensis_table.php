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
            
            // Perbaikan: Menghapus ->notNull() karena default Laravel sudah NOT NULL
            $table->enum('status', ['terbuka', 'ditutup'])->default('ditutup');
            
            // KONSOLIDASI PATCH QR CODE 2026 (Presisi sesuai tipe data berkas Anda)
            $table->text('qr_code')->nullable();
            $table->timestamp('qr_generated_at')->nullable();
            $table->timestamp('qr_expires_at')->nullable();
            
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('FK ke users (PIC/Panitia yang membuka sesi)');
            $table->timestamps();
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