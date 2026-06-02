<?php

namespace Database\Seeders;

use App\Models\Pengajuan;
use Illuminate\Database\Seeder;

class CheckPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('CHECKING PENGAJUAN DATA');
        $this->command->info('===========================================');

        $pengajuans = Pengajuan::with('user')->orderBy('created_at', 'desc')->get();

        if ($pengajuans->isEmpty()) {
            $this->command->warn('No pengajuan found!');

            return;
        }

        foreach ($pengajuans as $p) {
            $this->command->info('');
            $this->command->info("Judul: {$p->judul_rencana}");
            $this->command->info("  Pengaju: {$p->user->name} ({$p->user->email})");
            $this->command->info("  Status: {$p->status_pengajuan}");
            $this->command->info("  Created: {$p->created_at}");

            if ($p->status_pengajuan === 'disetujui_final') {
                $this->command->info('  ✓ Status FINAL - Seharusnya ada bimtek');

                if ($p->bimtek) {
                    $this->command->info("  ✓ Bimtek exists: {$p->bimtek->judul_final}");
                    $this->command->info("    PIC: {$p->bimtek->pic->name}");
                } else {
                    $this->command->error('  ✗ Bimtek NOT CREATED! (BUG?)');
                }
            }
        }

        $this->command->info('');
        $this->command->info('===========================================');
    }
}
