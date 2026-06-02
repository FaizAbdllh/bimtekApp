<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivationToken;

$raw = $argv[1] ?? null;
if (! $raw) {
    echo "Usage: php tools/scripts/check_token.php <raw_token>\n";
    exit(2);
}

$token = ActivationToken::findByRawToken($raw);
if (! $token) {
    echo "Token not found\n";
    exit(3);
}

echo 'id: '.$token->id.PHP_EOL;
echo 'user_id: '.$token->user_id.PHP_EOL;
echo 'bimtek_id: '.($token->bimtek_id ?? '-').PHP_EOL;
echo 'expires_at: '.($token->expires_at ? $token->expires_at->toDateTimeString() : '-').PHP_EOL;
echo 'used_at: '.($token->used_at ? $token->used_at->toDateTimeString() : '-').PHP_EOL;
echo 'revoked_at: '.($token->revoked_at ? $token->revoked_at->toDateTimeString() : '-').PHP_EOL;
