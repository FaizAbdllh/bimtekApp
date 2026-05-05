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
        Schema::create('fasilitas_logistiks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengajuan_id')->constrained('pengajuans')->onDelete('cascade');
            $table->string('nama_fasilitas');
            $table->integer('jumlah')->default(1);
            $table->enum('status', ['diminta', 'tersedia', 'tidak_tersedia'])->default('diminta');
            $table->text('catatan_rt')->nullable(); // Catatan dari Koordinator RT
            $table->timestamps();
        });

        // Tambahkan kolom catatan_logistik ke tabel pengajuans
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->text('catatan_logistik')->nullable()->after('catatan_rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn('catatan_logistik');
        });
        
        Schema::dropIfExists('fasilitas_logistiks');
    }
};
