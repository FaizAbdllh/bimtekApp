<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSistem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'log_sistems';

    protected $fillable = [
        'user_id',
        'level',
        'pesan',
    ];

    /**
     * Get the user who triggered this log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create an info log.
     */
    public static function info(string $pesan, ?string $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'level' => 'info',
            'pesan' => $pesan,
        ]);
    }

    /**
     * Create a warning log.
     */
    public static function warning(string $pesan, ?string $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'level' => 'warning',
            'pesan' => $pesan,
        ]);
    }

    /**
     * Create an error log.
     */
    public static function error(string $pesan, ?string $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'level' => 'error',
            'pesan' => $pesan,
        ]);
    }
}
