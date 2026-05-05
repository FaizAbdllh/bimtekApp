<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate PIC data from bimtek_user pivot to bimteks.pic_user_id
        DB::table('bimtek_user')
            ->where('peran_kontekstual', 'pic')
            ->get()
            ->each(function ($pivot) {
                DB::table('bimteks')
                    ->where('id', $pivot->bimtek_id)
                    ->update(['pic_user_id' => $pivot->user_id]);
            });
        
        // Delete pic records from bimtek_user
        DB::table('bimtek_user')
            ->where('peran_kontekstual', 'pic')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore pic records to bimtek_user
        DB::table('bimteks')
            ->whereNotNull('pic_user_id')
            ->get()
            ->each(function ($bimtek) {
                DB::table('bimtek_user')->insert([
                    'bimtek_id' => $bimtek->id,
                    'user_id' => $bimtek->pic_user_id,
                    'peran_kontekstual' => 'pic',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        
        // Clear pic_user_id from bimteks
        DB::table('bimteks')->update(['pic_user_id' => null]);
    }
};
