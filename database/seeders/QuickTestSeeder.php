<?php

namespace Database\Seeders;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QuickTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create PIC user
        $pic = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic@test.com',
            'password' => Hash::make('password'),
        ]);

        // Create Peserta user
        $peserta = User::factory()->create([
            'name' => 'Peserta Test',
            'email' => 'peserta@test.com',
            'password' => Hash::make('password'),
        ]);

        // Create Pengajuan
        $pengajuan = Pengajuan::factory()->create([
            'pengaju_user_id' => $pic->id,
            'status' => 'disetujui_rt',
        ]);

        // Create Bimtek with document verification
        $bimtek = Bimtek::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'pic_user_id' => $pic->id,
            'status' => 'dibuka',
            'memerlukan_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['surat_tugas', 'sppd'],
        ]);

        // Assign peserta dengan status 'invited'
        $bimtek->users()->attach($peserta->id, [
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => 'invited',
            'notified_at' => now(),
        ]);

        $this->command->info('===========================================');
        $this->command->info('✓ Test data created successfully!');
        $this->command->info('===========================================');
        $this->command->info('PIC Login:');
        $this->command->info('  Email: pic@test.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Peserta Login:');
        $this->command->info('  Email: peserta@test.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Bimtek ID: ' . $bimtek->id);
        $this->command->info('Bimtek: ' . $bimtek->judul_final);
        $this->command->info('===========================================');
    }
}
