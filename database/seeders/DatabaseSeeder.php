<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Seed SBM master reference data
        $this->call(SbmMasterSeeder::class);

        $this->seedUsers();
    }

    /**
     * Seed sample users.
     */
    private function seedUsers(): void
    {
        // Create Admin IT user
        $adminRole = Role::where('nama_peran', 'Admin IT')->first();
        User::firstOrCreate(
            ['email' => 'admin@bbpmp.go.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'nip' => '199001012020011001',
                'asal_instansi' => 'BBPMP Sumbar',
                'role_id' => $adminRole->id,
            ]
        );

        // Create Kepala user
        $kepalaRole = Role::where('nama_peran', 'Kepala')->first();
        User::firstOrCreate(
            ['email' => 'kepala@bbpmp.go.id'],
            [
                'name' => 'Kepala BBPMP',
                'password' => Hash::make('password'),
                'nip' => '197001011995011001',
                'asal_instansi' => 'BBPMP Sumbar',
                'role_id' => $kepalaRole->id,
            ]
        );

        // Create PPK user
        $ppkRole = Role::where('nama_peran', 'PPK')->first();
        User::firstOrCreate(
            ['email' => 'ppk@bbpmp.go.id'],
            [
                'name' => 'PPK BBPMP',
                'password' => Hash::make('password'),
                'nip' => '198001012005011001',
                'asal_instansi' => 'BBPMP Sumbar',
                'role_id' => $ppkRole->id,
            ]
        );

        // Create Koordinator RT user
        $rtRole = Role::where('nama_peran', 'Koordinator RT')->first();
        User::firstOrCreate(
            ['email' => 'rt@bbpmp.go.id'],
            [
                'name' => 'Koordinator Rumah Tangga',
                'password' => Hash::make('password'),
                'nip' => '198501012010011001',
                'asal_instansi' => 'BBPMP Sumbar',
                'role_id' => $rtRole->id,
            ]
        );

        // Create sample Pegawai Internal user
        $pegawaiRole = Role::where('nama_peran', 'Pegawai Internal')->first();
        User::firstOrCreate(
            ['email' => 'pegawai@bbpmp.go.id'],
            [
                'name' => 'Pegawai Internal',
                'password' => Hash::make('password'),
                'nip' => '199001012015011001',
                'asal_instansi' => 'BBPMP Sumbar',
                'role_id' => $pegawaiRole->id,
            ]
        );

        // Create sample Peserta Eksternal user
        $eksternalRole = Role::where('nama_peran', 'Peserta Eksternal')->first();
        User::firstOrCreate(
            ['email' => 'peserta@gmail.com'],
            [
                'name' => 'Peserta Eksternal',
                'password' => Hash::make('password'),
                'nip' => null,
                'asal_instansi' => 'SDN 01 Padang',
                'role_id' => $eksternalRole->id,
            ]
        );
    }
}
