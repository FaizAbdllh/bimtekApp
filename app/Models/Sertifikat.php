<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sertifikats';

    protected $fillable = [
        'bimtek_id',
        'user_id',
        'nomor_sertifikat',
        'tanggal_terbit',
        'file_path',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    /**
     * Get the bimtek that owns the sertifikat.
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class);
    }

    /**
     * Get the user (peserta) who owns this sertifikat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - the peserta.
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
