<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteRoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Get all roles
        $adminItRole = Role::where('nama_peran', 'Admin IT')->first();
        $kepalaRole = Role::where('nama_peran', 'Kepala')->first();
        $ppkRole = Role::where('nama_peran', 'PPK')->first();
        $rtRole = Role::where('nama_peran', 'Koordinator RT')->first();
        $pegawaiRole = Role::where('nama_peran', 'Pegawai Internal')->first();
        $eksternalRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

        // Create or update users for each role
        $users = [
            [
                'name' => 'Admin IT',
                'email' => 'admin@test.com',
                'role_id' => $adminItRole->id,
            ],
            [
                'name' => 'Kepala Satuan Kerja',
                'email' => 'kepala@test.com',
                'role_id' => $kepalaRole->id,
            ],
            [
                'name' => 'PPK',
                'email' => 'ppk@test.com',
                'role_id' => $ppkRole->id,
            ],
            [
                'name' => 'Koordinator RT',
                'email' => 'rt@test.com',
                'role_id' => $rtRole->id,
            ],
            [
                'name' => 'Pegawai Internal',
                'email' => 'pegawai@test.com',
                'role_id' => $pegawaiRole->id,
            ],
            [
                'name' => 'Peserta Eksternal',
                'email' => 'eksternal@test.com',
                'role_id' => $eksternalRole->id,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'role_id' => $userData['role_id'],
                ]
            );

            $this->command->info("✓ Created/Updated: {$userData['email']} ({$userData['name']})");
        }

        // Update existing pic@test.com and peserta@test.com to proper names
        $pic = User::where('email', 'pic@test.com')->first();
        if ($pic) {
            $pic->name = 'PIC Bimtek';
            $pic->role_id = $pegawaiRole->id; // PIC is Pegawai Internal
            $pic->save();
            $this->command->info('✓ Updated: pic@test.com (PIC Bimtek)');
        }

        $peserta = User::where('email', 'peserta@test.com')->first();
        if ($peserta) {
            $peserta->name = 'Peserta Test';
            $peserta->role_id = $pegawaiRole->id; // Peserta internal
            $peserta->save();
            $this->command->info('✓ Updated: peserta@test.com (Peserta Test)');
        }

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('✓ All role users created/updated!');
        $this->command->info('===========================================');
        $this->command->info('Login credentials (all passwords: password)');
        $this->command->info('');
        $this->command->info('Admin IT:           admin@test.com');
        $this->command->info('Kepala:             kepala@test.com');
        $this->command->info('PPK:                ppk@test.com');
        $this->command->info('Koordinator RT:     rt@test.com');
        $this->command->info('Pegawai Internal:   pegawai@test.com');
        $this->command->info('Peserta Eksternal:  eksternal@test.com');
        $this->command->info('PIC Bimtek:         pic@test.com');
        $this->command->info('Peserta Test:       peserta@test.com');
        $this->command->info('===========================================');
    }
}
