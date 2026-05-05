<?php

namespace Database\Seeders;

use App\Models\LogSistem;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogSistemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        // Sample log entries
        $logs = [
            // Info logs
            ['level' => 'info', 'pesan' => 'User berhasil login ke sistem'],
            ['level' => 'info', 'pesan' => 'Pengajuan bimtek baru telah dibuat dengan nomor REQ-2024-001'],
            ['level' => 'info', 'pesan' => 'Bimtek "Pelatihan Kurikulum Merdeka" telah disetujui oleh Kepala'],
            ['level' => 'info', 'pesan' => 'Materi pembelajaran berhasil diupload: Modul 1 - Pengantar'],
            ['level' => 'info', 'pesan' => 'Absensi sesi pagi berhasil dibuka untuk bimtek "Workshop Asesmen Nasional"'],
            ['level' => 'info', 'pesan' => 'Sertifikat peserta berhasil digenerate untuk 50 peserta'],
            ['level' => 'info', 'pesan' => 'User baru berhasil ditambahkan: ahmad.pratama@kemdikbud.go.id'],
            ['level' => 'info', 'pesan' => 'Password user berhasil direset oleh Admin IT'],
            ['level' => 'info', 'pesan' => 'Export laporan rekap peserta berhasil didownload'],
            ['level' => 'info', 'pesan' => 'Template sertifikat baru berhasil diupload'],
            
            // Warning logs
            ['level' => 'warning', 'pesan' => 'User mencoba login dengan password salah sebanyak 3 kali'],
            ['level' => 'warning', 'pesan' => 'Kuota peserta bimtek hampir penuh (45/50 peserta)'],
            ['level' => 'warning', 'pesan' => 'Deadline pengumpulan tugas tinggal 1 hari'],
            ['level' => 'warning', 'pesan' => 'Storage hampir penuh: 85% terpakai'],
            ['level' => 'warning', 'pesan' => 'Beberapa peserta belum melakukan absensi sesi pagi'],
            
            // Error logs
            ['level' => 'error', 'pesan' => 'Gagal mengirim email notifikasi ke peserta: SMTP connection refused'],
            ['level' => 'error', 'pesan' => 'Upload materi gagal: File size melebihi batas maksimal 10MB'],
            ['level' => 'error', 'pesan' => 'Generate sertifikat gagal: Template tidak ditemukan'],
            ['level' => 'error', 'pesan' => 'Database connection timeout pada query pengajuan'],
        ];

        foreach ($logs as $index => $log) {
            LogSistem::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'level' => $log['level'],
                'pesan' => $log['pesan'],
                'created_at' => now()->subHours(rand(0, 72))->subMinutes(rand(0, 59)),
            ]);
        }

        // Add some system logs (no user)
        $systemLogs = [
            ['level' => 'info', 'pesan' => 'System startup completed'],
            ['level' => 'info', 'pesan' => 'Daily backup completed successfully'],
            ['level' => 'warning', 'pesan' => 'Scheduled maintenance in 24 hours'],
            ['level' => 'error', 'pesan' => 'Failed to connect to email service'],
        ];

        foreach ($systemLogs as $log) {
            LogSistem::create([
                'user_id' => null,
                'level' => $log['level'],
                'pesan' => $log['pesan'],
                'created_at' => now()->subHours(rand(0, 48)),
            ]);
        }
    }
}
