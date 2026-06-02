<?php

namespace Database\Seeders;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use Illuminate\Database\Seeder;

class CleanTestingDataSeeder extends Seeder
{
    /**
     * Clean testing bimtek and pengajuan data from seeders.
     */
    public function run(): void
    {
        // Delete bimtek created from seeders (not from real user pengajuan)
        $testingBimteks = Bimtek::whereHas('pengajuan', function ($q) {
            $q->whereIn('user_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('users')
                    ->whereIn('email', [
                        'pic@test.com',
                        'admin@test.com',
                        'kepala@test.com',
                        'ppk@test.com',
                    ]);
            });
        })->get();

        $bimtekCount = $testingBimteks->count();

        foreach ($testingBimteks as $bimtek) {
            $this->command->info("Deleting bimtek: {$bimtek->judul_final} (PIC: {$bimtek->pic->name})");
            $bimtek->delete();
        }

        // Delete testing pengajuan
        $testingPengajuans = Pengajuan::whereIn('user_id', function ($subQuery) {
            $subQuery->select('id')
                ->from('users')
                ->whereIn('email', [
                    'pic@test.com',
                    'admin@test.com',
                    'kepala@test.com',
                    'ppk@test.com',
                ]);
        })->get();

        $pengajuanCount = $testingPengajuans->count();

        foreach ($testingPengajuans as $pengajuan) {
            $this->command->info("Deleting pengajuan: {$pengajuan->judul_rencana} (Pengaju: {$pengajuan->user->name})");
            $pengajuan->delete();
        }

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info("✓ Deleted {$bimtekCount} testing bimtek(s)");
        $this->command->info("✓ Deleted {$pengajuanCount} testing pengajuan(s)");
        $this->command->info('===========================================');
        $this->command->info('Sekarang sistem siap untuk data real dari user!');
        $this->command->info('User dapat membuat pengajuan baru dan menjadi PIC setelah disetujui.');
    }
}
