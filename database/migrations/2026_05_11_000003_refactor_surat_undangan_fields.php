<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add draft/final surat undangan fields for split workflow
     * Note: Old file_surat_undangan_* fields are kept for backward compatibility
     */
    public function up(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            // Add draft fields (uploaded by PIC/Panitia)
            if (! Schema::hasColumn('bimteks', 'file_surat_draft_path')) {
                $table->string('file_surat_draft_path')->nullable();
            }
            if (! Schema::hasColumn('bimteks', 'file_surat_draft_uploaded_by')) {
                $table->foreignUuid('file_surat_draft_uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (! Schema::hasColumn('bimteks', 'file_surat_draft_uploaded_at')) {
                $table->timestamp('file_surat_draft_uploaded_at')->nullable();
            }

            // Add final fields (uploaded by Persuratan)
            if (! Schema::hasColumn('bimteks', 'file_surat_final_path')) {
                $table->string('file_surat_final_path')->nullable();
            }
            if (! Schema::hasColumn('bimteks', 'file_surat_final_uploaded_by')) {
                $table->foreignUuid('file_surat_final_uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (! Schema::hasColumn('bimteks', 'file_surat_final_uploaded_at')) {
                $table->timestamp('file_surat_final_uploaded_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            // Drop draft columns
            $draftColumns = [
                'file_surat_draft_path',
                'file_surat_draft_uploaded_by',
                'file_surat_draft_uploaded_at',
            ];

            foreach ($draftColumns as $column) {
                if (Schema::hasColumn('bimteks', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Drop final columns
            $finalColumns = [
                'file_surat_final_path',
                'file_surat_final_uploaded_by',
                'file_surat_final_uploaded_at',
            ];

            foreach ($finalColumns as $column) {
                if (Schema::hasColumn('bimteks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
