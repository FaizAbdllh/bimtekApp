<?php

namespace Database\Seeders;

use App\Models\Pengajuan;
use Illuminate\Database\Seeder;

class CheckPengajuanIdSeeder extends Seeder
{
    public function run(): void
    {
        $pengajuan = Pengajuan::with('user')->first();

        if ($pengajuan) {
            $this->command->info('===========================================');
            $this->command->info('PENGAJUAN DETAIL');
            $this->command->info('===========================================');
            $this->command->info("ID: {$pengajuan->id}");
            $this->command->info("Judul Rencana: {$pengajuan->judul_rencana}");
            $this->command->info("Status: {$pengajuan->status_pengajuan}");
            $this->command->info("User: {$pengajuan->user->name} ({$pengajuan->user->email})");
            $this->command->info("Created: {$pengajuan->created_at}");
            $this->command->info('');
            $this->command->info("Expected URL: /ppk/pengajuan/{$pengajuan->id}");
            $this->command->info('Route name: approval.ppk.show');
            $this->command->info('');

            // Test route generation
            $url = route('approval.ppk.show', $pengajuan->id);
            $this->command->info("Generated URL: {$url}");
            $this->command->info('===========================================');
        } else {
            $this->command->error('No pengajuan found!');
        }
    }
}
