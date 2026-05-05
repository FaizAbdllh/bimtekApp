<?php

namespace Database\Seeders;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManualTestSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $adminItRole = \App\Models\Role::where('nama_peran', 'Admin IT')->first();
        $kepalaSatkerRole = \App\Models\Role::where('nama_peran', 'Kepala Satuan Kerja')->first();
        $pegawaiRole = \App\Models\Role::where('nama_peran', 'Pegawai')->first();

        // Create users
        $adminIt = User::create([
            'name' => 'Admin IT',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $adminItRole?->id,
        ]);

        $pic = User::create([
            'name' => 'PIC Bimtek',
            'email' => 'pic@test.com',
            'password' => Hash::make('password'),
            'role_id' => $kepalaSatkerRole?->id,
        ]);

        $peserta = User::create([
            'name' => 'Peserta Test',
            'email' => 'peserta@test.com',
            'password' => Hash::make('password'),
            'role_id' => $pegawaiRole?->id,
        ]);

        // Create pengajuan
        $pengajuan = Pengajuan::create([
            'judul' => 'Bimtek Testing Verifikasi Dokumen',
            'latar_belakang' => 'Testing manual fitur verifikasi dokumen peserta',
            'tujuan' => 'Memastikan semua fitur berjalan dengan baik',
            'sasaran_peserta' => 'Pegawai internal',
            'target_jumlah_peserta' => 20,
            'tanggal_mulai_usulan' => now()->addDays(30),
            'tanggal_selesai_usulan' => now()->addDays(32),
            'durasi_hari' => 3,
            'status' => 'disetujui_rt',
            'pengaju_user_id' => $pic->id,
        ]);

        // Create bimtek with document verification enabled
        $bimtek = Bimtek::create([
            'pengajuan_id' => $pengajuan->id,
            'pic_user_id' => $pic->id,
            'judul_final' => 'Bimtek Testing Verifikasi Dokumen',
            'deskripsi' => 'Bimtek untuk testing fitur verifikasi dokumen',
            'tanggal_mulai' => now()->addDays(30),
            'tanggal_selesai' => now()->addDays(32),
            'lokasi' => 'Ruang Meeting Lt. 3',
            'target_peserta' => 20,
            'status' => 'dibuka',
            'memerlukan_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['surat_tugas', 'sppd'],
        ]);

        // Assign peserta to bimtek with 'invited' status
        $bimtek->users()->attach($peserta->id, [
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => 'invited',
            'notified_at' => now(),
        ]);

        $this->command->info('✓ Users created:');
        $this->command->info('  - Admin IT: admin@test.com / password');
        $this->command->info('  - PIC: pic@test.com / password');
        $this->command->info('  - Peserta: peserta@test.com / password');
        $this->command->info('✓ Bimtek created with document verification enabled');
        $this->command->info('✓ Peserta assigned with "invited" status');
    }
}
