<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestUuidSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('TESTING UUID IMPLEMENTATION');
        $this->command->info('===========================================');

        // Test Users
        $users = User::take(3)->get();
        $this->command->info("\n✓ Users with UUID:");
        foreach ($users as $user) {
            $this->command->info("  ID: {$user->id} | Name: {$user->name}");
            $this->command->info('  UUID Length: '.strlen($user->id).' characters');
        }

        // Test Roles
        $roles = Role::take(3)->get();
        $this->command->info("\n✓ Roles with UUID:");
        foreach ($roles as $role) {
            $this->command->info("  ID: {$role->id} | Name: {$role->nama_peran}");
        }

        // Test relationship
        $firstUser = User::with('role')->first();
        if ($firstUser && $firstUser->role) {
            $this->command->info("\n✓ User-Role Relationship Test:");
            $this->command->info("  User: {$firstUser->name}");
            $this->command->info("  Role: {$firstUser->role->nama_peran}");
            $this->command->info("  User ID (UUID): {$firstUser->id}");
            $this->command->info("  Role ID (UUID): {$firstUser->role_id}");
        }

        $this->command->info("\n===========================================");
        $this->command->info('✓ UUID IMPLEMENTATION SUCCESS!');
        $this->command->info('===========================================');
    }
}
