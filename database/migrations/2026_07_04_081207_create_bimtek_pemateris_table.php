<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimtek_pemateris', function (Blueprint $table) {
            $table->id();
            
            // 💡 SOLUSI MUTLAK: Menggunakan foreignUuid agar tipe data kembar 
            // dengan primary key UUID milik tabel bimteks Anda.
            $table->foreignUuid('bimtek_id')
                ->nullable()
                ->constrained('bimteks')
                ->onDelete('cascade');
            
            $table->string('nama_pemateri', 255);
            $table->string('asal_instansi', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimtek_pemateris');
    }
};