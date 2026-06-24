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
        Schema::create('bimtek_panitias', function (Blueprint $table) {
            // Relasi Utama
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');

            // Atribut Spesifik Kepanitiaan (Hasil Konsolidasi Patch)
            $table->string('fungsi_panitia', 100)->nullable()->comment('Contoh: Sekretaris, Logistik, Konsumsi');
            
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
        Schema::dropIfExists('bimtek_panitias');
    }
};