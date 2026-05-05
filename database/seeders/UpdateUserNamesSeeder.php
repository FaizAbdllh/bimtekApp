<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateUserNamesSeeder extends Seeder
{
    public function run(): void
    {
        // Update user names to be more descriptive
        $updates = [
            'pic@test.com' => 'Ahmad Rizki Pratama',
            'peserta@test.com' => 'Siti Nurhaliza',
            'admin@test.com' => 'Budi Santoso',
            'kepala@test.com' => 'Dr. Hendra Wijaya',
            'ppk@test.com' => 'Rina Marlina, S.E.',
            'rt@test.com' => 'Agus Setiawan',
            'pegawai@test.com' => 'Dewi Kartika',
            'eksternal@test.com' => 'Muhammad Fauzi',
        ];

        foreach ($updates as $email => $name) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->name = $name;
                $user->save();
                $this->command->info("✓ Updated: {$email} → {$name}");
            }
        }

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('✓ User names updated successfully!');
        $this->command->info('===========================================');
    }
}
