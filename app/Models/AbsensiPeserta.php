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
        'bukti_hadir_online_path',
    ];

    /**
     * Get the sesi absensi that owns the absensi.
     *
     * @return BelongsTo<SesiAbsensi, AbsensiPeserta>
     */
    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(SesiAbsensi::class);
    }

    /**
     * Get the user (peserta) who attended.
     *
     * @return BelongsTo<User, AbsensiPeserta>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - the peserta.
     *
     * @return BelongsTo<User, AbsensiPeserta>
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
