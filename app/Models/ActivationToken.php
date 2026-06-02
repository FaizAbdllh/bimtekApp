<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivationToken extends Model
{
    use HasUuids;

    protected $table = 'activation_tokens';

    protected $fillable = [
        'id', 'user_id', 'bimtek_id', 'token_hash', 'expires_at', 'used_at', 'created_by', 'revoked_at', 'revoked_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateFor(User $user, ?int $days = 7, $createdBy = null)
    {
        $raw = Str::random(48);
        $hash = hash('sha256', $raw);

        $token = self::create([
            'user_id' => $user->id,
            'token_hash' => $hash,
            'expires_at' => now()->addDays($days),
            'created_by' => $createdBy,
        ]);

        return [$token, $raw];
    }

    public static function findByRawToken(string $raw)
    {
        $hash = hash('sha256', $raw);

        return self::where('token_hash', $hash)->first();
    }

    public function markUsed()
    {
        try {
            $this->used_at = now();
            $this->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRevoked()
    {
        return ! is_null($this->revoked_at);
    }
}
