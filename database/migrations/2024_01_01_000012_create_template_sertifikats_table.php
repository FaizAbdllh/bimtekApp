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
        Schema::create('template_sertifikats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_template')->comment('Contoh: Template Sertifikat internal, Template Luar');
            
            // Perbaikan: Menghapus ->notNull() agar tidak melempar BadMethodCallException
            $table->string('file_path');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_sertifikats');
    }
};