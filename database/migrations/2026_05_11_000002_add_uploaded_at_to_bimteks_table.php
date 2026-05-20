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
            $table->timestamp('file_surat_undangan_uploaded_at')->nullable()->after('file_surat_undangan_uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropColumn('file_surat_undangan_uploaded_at');
        });
    }
};
