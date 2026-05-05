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
        Schema::create('sbm_masters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_sbm', 50)->unique()->comment('Kode unik SBM, ex: HON-NRS-PUSAT');
            $table->string('nama_item')->comment('Nama item sesuai PMK 32/2025');
            $table->decimal('harga_satuan', 15, 2)->comment('Harga satuan dari SBM');
            $table->string('satuan_primary', 100)->comment('Satuan utama, ex: Orang/Jam (OJ)');
            $table->string('satuan_secondary', 100)->nullable()->comment('Satuan kedua jika ada');
            $table->enum('kategori', ['honor', 'transportasi', 'akomodasi', 'konsumsi', 'atk', 'sewa', 'lainnya'])->default('lainnya');
            $table->year('tahun_berlaku')->default(2025)->comment('Tahun berlaku PMK');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk performa
            $table->index('kategori');
            $table->index('is_active');
            $table->index(['tahun_berlaku', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sbm_masters');
    }
};
