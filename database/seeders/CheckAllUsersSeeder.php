<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CheckAllUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('ALL USERS IN DATABASE');
        $this->command->info('===========================================');

        $users = User::with('role')->orderBy('created_at', 'desc')->get();

        foreach ($users as $user) {
            $this->command->info('');
            $this->command->info("Name: {$user->name}");
            $this->command->info("  Email: {$user->email}");
            $this->command->info('  Role: '.($user->role ? $user->role->nama_peran : 'No Role'));
            $this->command->info("  Created: {$user->created_at}");
            $this->command->info('  Pengajuan: '.$user->pengajuans()->count());
            $this->command->info('  Bimtek as PIC: '.\App\Models\Bimtek::where('pic_user_id', $user->id)->count());
        }

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('Total Users: '.$users->count());
    }
}
