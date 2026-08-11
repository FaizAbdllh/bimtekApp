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
        Schema::create('bimtek_pesertas', function (Blueprint $table) {
            // Relasi Utama
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');

            // Atribut Spesifik Kepesertaan (Hasil Konsolidasi Patch)
            $table->enum('status_verifikasi', ['invited', 'pending', 'verified', 'rejected'])->nullable();
            $table->timestamp('notified_at')->nullable();
            
            $table->timestamps();

            // PENGUNCIAN: Composite Primary Key
            $table->primary(['bimtek_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimtek_pesertas');
    }
};