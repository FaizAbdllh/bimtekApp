<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DeleteDuplicateTestingUsersSeeder extends Seeder
{
    /**
     * Delete testing users that have the same roles as real users.
     */
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('DELETING DUPLICATE TESTING USERS');
        $this->command->info('===========================================');
        
        // List of testing user emails to delete (users with duplicate roles)
        $testingEmails = [
            // @test.com users with duplicate roles
            'kepala@test.com',       // Duplicate of muslih@gmail.com (Kepala)
            'ppk@test.com',          // Duplicate of citra@gmail.com (PPK)
            'rt@test.com',           // Duplicate of rudi@gmail.com (Koordinator RT)
            'pegawai@test.com',      // Duplicate of Salmawillis@gmail.com (Pegawai Internal)
            'eksternal@test.com',    // Duplicate of faiz@gmail.com (Peserta Eksternal)
            'pic@test.com',          // Testing user (Pegawai Internal)
            'peserta@test.com',      // Testing user (Pegawai Internal)
            
            // @bbpmp.go.id users (also testing data)
            'kepala@bbpmp.go.id',
            'ppk@bbpmp.go.id',
            'rt@bbpmp.go.id',
            'pegawai@bbpmp.go.id',
            'peserta@gmail.com',     // Testing peserta
            'admin@bbpmp.go.id',     // Keep only admin@test.com
        ];
        
        $deleted = 0;
        
        foreach ($testingEmails as $email) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $this->command->info("✓ Deleting: {$user->name} ({$email})");
                $user->delete();
                $deleted++;
            }
        }
        
        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info("✓ Deleted {$deleted} duplicate testing users");
        $this->command->info('===========================================');
        $this->command->info('');
        $this->command->info('Remaining users:');
        
        $remaining = User::with('role')->get();
        foreach ($remaining as $user) {
            $role = $user->role ? $user->role->nama_peran : 'No Role';
            $this->command->info("  - {$user->name} ({$user->email}) - {$role}");
        }
        
        $this->command->info('');
        $this->command->info('Total remaining: ' . $remaining->count());
    }
}
