<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivationToken;
use Illuminate\Support\Facades\Hash;

$raw = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (! $raw || ! $password) {
    echo "Usage: php tools/scripts/activate_token.php <raw_token> <new_password>\n";
    exit(2);
}

$token = ActivationToken::findByRawToken($raw);
if (! $token) {
    echo "Token not found\n";
    exit(3);
}
if ($token->used_at) {
    echo 'Token already used at: '.$token->used_at."\n";
    exit(4);
}
if ($token->isExpired()) {
    echo 'Token expired at: '.$token->expires_at."\n";
    exit(5);
}

// Update user password
$user = $token->user;
$user->password = Hash::make($password);
$user->save();

$token->markUsed();

echo "Activated user {$user->email}, token marked used.\n";
