<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the bimtek
$bimtekId = '019c51f0-1a33-71fe-b4ad-a2381460f047';
$bimtek = App\Models\Bimtek::with(['users', 'peserta', 'pic', 'panitia'])->find($bimtekId);

if (! $bimtek) {
    echo "Bimtek tidak ditemukan!\n";
    exit(1);
}

echo "=== DEBUG BIMTEK PESERTA ===\n\n";
echo "Bimtek: {$bimtek->judul_final}\n";
echo "ID: {$bimtek->id}\n\n";

echo "Total users di bimtek_user: {$bimtek->users->count()}\n";
echo "Total peserta (filtered): {$bimtek->peserta->count()}\n";
echo "Total panitia: {$bimtek->panitia->count()}\n";
echo 'PIC: '.($bimtek->pic ? $bimtek->pic->name : 'Tidak ada')."\n\n";

echo "=== Detail Users di bimtek_user ===\n";
foreach ($bimtek->users as $user) {
    echo "- {$user->name} (ID: {$user->id})\n";
    echo "  Email: {$user->email}\n";
    echo "  Peran: {$user->pivot->peran_kontekstual}\n";
    if (isset($user->pivot->status_verifikasi)) {
        echo "  Status Verifikasi: {$user->pivot->status_verifikasi}\n";
    }
    echo "\n";
}

echo "\n=== Peserta (dengan filter peran_kontekstual = 'peserta') ===\n";
if ($bimtek->peserta->count() == 0) {
    echo "TIDAK ADA PESERTA DALAM RELASI!\n";
} else {
    foreach ($bimtek->peserta as $peserta) {
        echo "- {$peserta->name}\n";
    }
}

echo "\n=== Panitia ===\n";
foreach ($bimtek->panitia as $panitia) {
    echo "- {$panitia->name}\n";
}
