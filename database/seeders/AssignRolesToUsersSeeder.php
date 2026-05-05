<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssignRolesToUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $pegawaiRole = Role::where('nama_peran', 'Pegawai Internal')->first();
        
        // Update all existing users without role
        $usersWithoutRole = User::whereNull('role_id')->get();
        
        foreach ($usersWithoutRole as $user) {
            $user->role_id = $pegawaiRole->id;
            $user->save();
            $this->command->info("✓ Assigned 'Pegawai Internal' role to: {$user->email}");
        }
        
        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('✓ All users now have roles assigned!');
        $this->command->info('===========================================');
    }
}
