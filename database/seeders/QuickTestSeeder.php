<?php

namespace Database\Seeders;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QuickTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create PIC user
        $pic = User::where('email', 'pic@test.com')->first();
        if (! $pic) {
            $pic = User::factory()->create([
                'name' => 'PIC Test',
                'email' => 'pic@test.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Create Peserta user
        $peserta = User::where('email', 'peserta@test.com')->first();
        if (! $peserta) {
            $peserta = User::factory()->create([
                'name' => 'Peserta Test',
                'email' => 'peserta@test.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Create Pengajuan (match current migrations)
        $pengajuan = Pengajuan::factory()->create([
            'user_id' => $pic->id,
            'status_pengajuan' => 'disetujui_final',
            'status_rt' => 'telah_dipenuhi',
        ]);

        // Create Bimtek with document verification
        $bimtek = Bimtek::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'pic_user_id' => $pic->id,
            'status_pelaksanaan' => 'persiapan',
            'butuh_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['surat_tugas', 'sppd'],
        ]);

        // Assign peserta dengan status 'invited'
        $bimtek->users()->attach($peserta->id, [
            'id' => (string) Str::uuid(),
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
        $this->command->info('Bimtek ID: '.$bimtek->id);
        $this->command->info('Bimtek: '.$bimtek->judul_final);
        $this->command->info('===========================================');
    }
}
