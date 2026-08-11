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

        // Definisikan satu password hash tunggal agar tidak membebani memori proses loop
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Yandri', // <-- Slot Admin IT Baru
                'email' => 'yandri@gmail.com',
                'role' => 'Admin IT',
                'asal_instansi' => 'BBPMP Sumbar',
            ],
            [
                'name' => 'Salmawilis',
                'email' => 'salma@gmail.com',
                'role' => 'Pegawai Internal',
                'asal_instansi' => 'BBPMP Sumbar',
            ],
            [
                'name' => 'Muslihuddin',
                'email' => 'muslih@gmail.com',
                'role' => 'Kepala',
                'asal_instansi' => 'BBPMP Sumbar',
            ],
            [
                'name' => 'Chitra Puspitahati',
                'email' => 'citra@gmail.com',
                'role' => 'PPK',
                'asal_instansi' => 'BBPMP Sumbar',
            ],
            [
                'name' => 'Rudi Fianto',
                'email' => 'rudi@gmail.com',
                'role' => 'Koordinator RT',
                'asal_instansi' => 'BBPMP Sumbar',
            ],
            [
                'name' => 'Faiz Abdullah',
                'email' => 'faiz@gmail.com',
                'role' => 'Peserta Eksternal',
                'asal_instansi' => 'Dinas Pendidikan',
            ],
        ];

        foreach ($users as $userData) {
            // Cari UUID role berdasarkan nama_peran di database
            $role = Role::where('nama_peran', $userData['role'])->first();

            if (!$role) {
                $this->command->error("✕ Peran [{$userData['role']}] tidak ditemukan! Lewati user: {$userData['name']}");
                continue;
            }

            // Menggunakan firstOrCreate agar aman dijalankan berulang kali tanpa merusak hash password
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword,
                    'role_id' => $role->id,
                    'asal_instansi' => $userData['asal_instansi'],
                    'nip' => null, // Bisa diupdate manual via simpeg/fitur profil nanti
                ]
            );

            $this->command->info("✓ Ready: {$user->name} ({$user->email}) - [{$userData['role']}]");
        }

        $this->command->info("\n===========================================");
        $this->command->info('✓ All real users processed successfully!');
        $this->command->info('===========================================');
    }
}