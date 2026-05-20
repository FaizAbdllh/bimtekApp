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
        Schema::table('bimteks', function (Blueprint $table) {
            $table->foreignUuid('file_surat_undangan_uploaded_by')->nullable()->after('file_surat_undangan_path')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropForeign(['file_surat_undangan_uploaded_by']);
            $table->dropColumn('file_surat_undangan_uploaded_by');
        });
    }
};
