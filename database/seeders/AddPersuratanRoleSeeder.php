<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddPersuratanRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert role if not exists
        $exists = DB::table('roles')->where('nama_peran', 'Persuratan')->exists();
        if (! $exists) {
            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'nama_peran' => 'Persuratan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
