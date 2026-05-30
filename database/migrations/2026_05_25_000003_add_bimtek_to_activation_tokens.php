<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('activation_tokens', 'bimtek_id')) {
            Schema::table('activation_tokens', function (Blueprint $table) {
                $table->uuid('bimtek_id')->nullable()->index()->after('user_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('activation_tokens', 'bimtek_id')) {
            Schema::table('activation_tokens', function (Blueprint $table) {
                $table->dropColumn('bimtek_id');
            });
        }
    }
};
