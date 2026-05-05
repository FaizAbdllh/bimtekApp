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
        Schema::create('materis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            $table->string('judul');
            $table->string('file_path')->notNull();
            $table->enum('tipe', ['materi', 'panduan'])->notNull()->comment('Membedakan materi dan panduan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
