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
        Schema::create('kebutuhan_anggarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bimtek_id')->constrained('bimteks')->onDelete('cascade');
            
            // Hubungan Relasi Validasi ke Master SBM (Hasil Patch)
            $table->foreignUuid('sbm_master_id')->nullable()->constrained('sbm_masters')->nullOnDelete();

            $table->string('nama_item');
            $table->integer('volume_1')->default(1);
            $table->string('satuan_1', 50)->nullable();
            $table->integer('volume_2')->default(1);
            $table->string('satuan_2', 50)->nullable();
            
            // Komponen Anggaran Riil
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_biaya', 15, 2)->comment('Hasil kalkulasi: vol1 * vol2 * harga');
            $table->enum('kategori', ['honor', 'transportasi', 'akomodasi', 'konsumsi', 'atk', 'sewa', 'lainnya']);
            
            // Komponen Validasi Deviasi Finansial SBM (Hasil Patch)
            $table->decimal('harga_satuan_sbm', 15, 2)->nullable()->comment('Harga pembanding SBM');
            $table->enum('status_validasi', ['sesuai_sbm', 'deviasi_minor', 'deviasi_major', 'deviasi_signifikan', 'non_sbm'])->default('non_sbm');
            $table->decimal('persentase_deviasi', 5, 2)->nullable();
            $table->text('justifikasi_deviasi')->nullable();
            
            // Aktor Pengesah Anggaran Over-Budget
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // --- INDEX PERFORMA ---
            $table->index('kategori');
            $table->index('status_validasi');
            $table->index('sbm_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_anggarans');
    }
};