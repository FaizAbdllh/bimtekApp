<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivationToken;
use App\Models\User;

$email = $argv[1] ?? 'peserta@gmail.com';
$days = isset($argv[2]) ? (int) $argv[2] : 7;

$user = User::where('email', $email)->first();
if (! $user) {
    echo "ERROR: user not found: $email\n";
    exit(2);
}

[$token, $raw] = ActivationToken::generateFor($user, $days, null);
// Optionally associate bimtek id if provided as 3rd arg
if (isset($argv[3])) {
    $token->bimtek_id = $argv[3];
    $token->save();
}

echo $raw.PHP_EOL;
