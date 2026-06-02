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
        Schema::table('kebutuhan_anggarans', function (Blueprint $table) {
            $table->foreignUuid('sbm_master_id')->nullable()->after('pengajuan_id')->constrained('sbm_masters')->nullOnDelete();
            $table->decimal('harga_satuan_sbm', 15, 2)->nullable()->after('harga_satuan')->comment('Harga satuan dari SBM untuk perbandingan');
            $table->enum('status_validasi', ['sesuai_sbm', 'deviasi_minor', 'deviasi_major', 'deviasi_signifikan', 'non_sbm'])->default('non_sbm')->after('kategori');
            $table->decimal('persentase_deviasi', 5, 2)->nullable()->after('status_validasi')->comment('Persentase deviasi dari SBM');
            $table->text('justifikasi_deviasi')->nullable()->after('persentase_deviasi')->comment('Alasan jika ada deviasi dari SBM');
            $table->foreignUuid('approved_by')->nullable()->after('justifikasi_deviasi')->constrained('users')->nullOnDelete()->comment('User yang approve deviasi');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Index untuk query performa
            $table->index('status_validasi');
            $table->index('sbm_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kebutuhan_anggarans', function (Blueprint $table) {
            $table->dropForeign(['sbm_master_id']);
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['status_validasi']);
            $table->dropIndex(['sbm_master_id']);
            $table->dropColumn([
                'sbm_master_id',
                'harga_satuan_sbm',
                'status_validasi',
                'persentase_deviasi',
                'justifikasi_deviasi',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};
