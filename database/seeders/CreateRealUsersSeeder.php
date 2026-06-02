<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateRealUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('CREATING REAL USER ACCOUNTS (UUID)');
        $this->command->info("===========================================\n");

        $users = [
            [
                'name' => 'Salmawilis',
                'email' => 'Salmawillis@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Pegawai Internal',
            ],
            [
                'name' => 'Muslihuddin',
                'email' => 'muslih@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Kepala',
            ],
            [
                'name' => 'Chitra Puspitahati',
                'email' => 'citra@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'PPK',
            ],
            [
                'name' => 'Rudi Fianto',
                'email' => 'rudi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Koordinator RT',
            ],
            [
                'name' => 'Faiz Abdullah',
                'email' => 'faiz@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Peserta Eksternal',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('nama_peran', $userData['role'])->first();

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'role_id' => $role->id,
                ]
            );

            $this->command->info("✓ Created: {$user->name} ({$user->email}) - {$userData['role']}");
            $this->command->info("  UUID: {$user->id}");
        }

        $this->command->info("\n===========================================");
        $this->command->info('✓ All real users created with UUID!');
        $this->command->info('All passwords: password');
        $this->command->info('===========================================');
    }
}
