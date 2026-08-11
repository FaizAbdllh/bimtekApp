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
        Schema::create('log_sistems', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Perbaikan: Menghapus ->notNull() bawaan lama
            $table->string('level', 50);
            $table->text('pesan');
            
            $table->timestamp('created_at')->nullable()->comment('Menggunakan created_at saja');
            $table->timestamp('updated_at')->nullable();

            // --- KONSOLIDASI INDEX PERFORMA (Dari file penambal 2024) ---
            $table->index('level');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_sistems');
    }
};