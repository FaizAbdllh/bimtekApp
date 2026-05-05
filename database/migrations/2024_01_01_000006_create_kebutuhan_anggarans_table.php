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
            $table->foreignUuid('pengajuan_id')->constrained('pengajuans')->onDelete('cascade');
            $table->string('nama_item');
            $table->integer('volume_1')->default(1)->comment('Misal: Jumlah Orang');
            $table->string('satuan_1', 50)->nullable()->comment('Misal: Org, Buah');
            $table->integer('volume_2')->default(1)->comment('Misal: Jumlah Hari/Kegiatan');
            $table->string('satuan_2', 50)->nullable()->comment('Misal: Hari, Keg, Paket');
            $table->decimal('harga_satuan', 15, 2)->notNull();
            $table->decimal('total_biaya', 15, 2)->notNull()->comment('Hasil kali vol1 * vol2 * harga');
            $table->enum('kategori', ['honor', 'transportasi', 'akomodasi', 'konsumsi', 'atk', 'sewa', 'lainnya'])->notNull();
            $table->timestamps();
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
