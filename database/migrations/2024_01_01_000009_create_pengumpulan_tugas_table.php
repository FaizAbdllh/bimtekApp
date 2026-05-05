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
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_jawaban_path')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignUuid('user_id_penilai')->nullable()->constrained('users')->onDelete('set null')->comment('FK ke users (PIC/Panitia yang menilai)');
            $table->timestamps();

            // Satu user hanya bisa mengumpulkan satu jawaban per tugas
            $table->unique(['tugas_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
