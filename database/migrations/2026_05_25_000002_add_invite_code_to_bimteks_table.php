<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bimteks', function (Blueprint $table) {
            if (! Schema::hasColumn('bimteks', 'invite_code')) {
                $table->string('invite_code', 64)->nullable()->unique()->after('virtual_meeting_url');
            }
        });
    }

    public function down()
    {
        Schema::table('bimteks', function (Blueprint $table) {
            if (Schema::hasColumn('bimteks', 'invite_code')) {
                $table->dropColumn('invite_code');
            }
        });
    }
};
