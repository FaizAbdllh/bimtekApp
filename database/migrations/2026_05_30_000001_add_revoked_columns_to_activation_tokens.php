<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('activation_tokens', 'revoked_at')) {
            Schema::table('activation_tokens', function (Blueprint $table) {
                $table->timestamp('revoked_at')->nullable()->after('used_at');
                $table->uuid('revoked_by')->nullable()->after('revoked_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('activation_tokens', 'revoked_at')) {
            Schema::table('activation_tokens', function (Blueprint $table) {
                $table->dropColumn(['revoked_at', 'revoked_by']);
            });
        }
    }
};
