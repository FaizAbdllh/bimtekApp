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
            $table->foreignUuid('pic_user_id')->nullable()->after('pengajuan_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimteks', function (Blueprint $table) {
            $table->dropForeign(['pic_user_id']);
            $table->dropColumn('pic_user_id');
        });
    }
};
