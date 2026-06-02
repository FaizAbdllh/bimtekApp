<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nama_peran' => 'Admin IT'],
            ['nama_peran' => 'Kepala'],
            ['nama_peran' => 'PPK'],
            ['nama_peran' => 'Koordinator RT'],
            ['nama_peran' => 'Pegawai Internal'],
            ['nama_peran' => 'Peserta Eksternal'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
