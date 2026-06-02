<?php

namespace Database\Seeders;

use App\Models\Bimtek;
use Illuminate\Database\Seeder;

class CheckBimtekPicSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('CHECKING BIMTEK DATA');
        $this->command->info('===========================================');

        $bimteks = Bimtek::with(['pic', 'pengajuan.user'])->get();

        if ($bimteks->isEmpty()) {
            $this->command->warn('No bimtek found in database!');

            return;
        }

        foreach ($bimteks as $bimtek) {
            $this->command->info('');
            $this->command->info("Bimtek: {$bimtek->judul_final}");
            $this->command->info("  ID: {$bimtek->id}");
            $this->command->info("  Status: {$bimtek->status_pelaksanaan}");

            if ($bimtek->pengajuan) {
                $pengaju = $bimtek->pengajuan->user;
                $this->command->info("  Pengaju (dari pengajuan): {$pengaju->name} ({$pengaju->email})");
            }

            if ($bimtek->pic) {
                $this->command->info("  PIC (pic_user_id): {$bimtek->pic->name} ({$bimtek->pic->email})");
            } else {
                $this->command->error('  PIC: NULL or NOT FOUND!');
            }

            // Check if pic matches pengaju
            if ($bimtek->pengajuan && $bimtek->pic) {
                if ($bimtek->pic_user_id === $bimtek->pengajuan->user_id) {
                    $this->command->info('  ✓ PIC = Pengaju (CORRECT)');
                } else {
                    $this->command->error('  ✗ PIC ≠ Pengaju (MISMATCH!)');
                    $this->command->error("    pic_user_id: {$bimtek->pic_user_id}");
                    $this->command->error("    pengajuan.user_id: {$bimtek->pengajuan->user_id}");
                }
            }
        }

        $this->command->info('');
        $this->command->info('===========================================');
    }
}
