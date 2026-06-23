<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivationToken extends Model
{
    use HasUuids;

    /**
     * @property string $id
     * @property string $user_id
     * @property string|null $bimtek_id
     * @property string $token_hash
     * @property \Illuminate\Support\Carbon|null $expires_at
     * @property \Illuminate\Support\Carbon|null $used_at
     * @property \Illuminate\Support\Carbon|null $revoked_at
     * @property \App\Models\User $user
     * @method static self create(array $attributes = [])
     * @method static \Illuminate\Database\Eloquent\Builder|self where(string $column, mixed $value)
     */

    protected $table = 'activation_tokens';

    protected $fillable = [
        'id', 'user_id', 'bimtek_id', 'token_hash', 'expires_at', 'used_at', 'created_by', 'revoked_at', 'revoked_by',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, ActivationToken>
     */

    /**
     * @param User $user
     * @param int|null $days
     * @param string|null $createdBy
     * @return array{0: self,1: string}
     */
    public static function generateFor(User $user, ?int $days = 7, ?string $createdBy = null): array
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

    /**
     * @param string $raw
     * @return ?self
     */
    public static function findByRawToken(string $raw): ?self
    {
        $hash = hash('sha256', $raw);

        return self::where('token_hash', $hash)->first();
    }

    public function markUsed(): void
    {
        // Atomically mark token as used only if it wasn't used yet.
        $updated = static::query()
            ->where('id', $this->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        if ($updated) {
            $this->used_at = now();
        } else {
            throw new \RuntimeException('Activation token already used or failed to mark.');
        }
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return ! is_null($this->revoked_at);
    }
}
