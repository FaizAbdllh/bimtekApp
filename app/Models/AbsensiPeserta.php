<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPeserta extends Model
{
    use HasFactory;

    protected $table = 'absensi_pesertas';

    // Nonaktifkan auto-increment karena menggunakan composite key
    public $incrementing = false;
    protected $keyType = 'string';

    // Eloquent tetap bisa membaca data, relasi, dan mendefinisikan fillable
    protected $fillable = [
        'sesi_absensi_id',
        'bimtek_id',
        'user_id',
        'bukti_hadir_online_path',
        'status_kehadiran',
        'waktu_presensi',
    ];

    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(SesiAbsensi::class, 'sesi_absensi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}