<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPeserta extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'absensi_pesertas';

    protected $fillable = [
        'sesi_absensi_id',
        'user_id',
    ];

    /**
     * Get the sesi absensi that owns the absensi.
     */
    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(SesiAbsensi::class);
    }

    /**
     * Get the user (peserta) who attended.
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
