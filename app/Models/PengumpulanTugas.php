<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    // Menonaktifkan auto-increment dan menetapkan key type string karena menggunakan composite key tanpa kolom id tunggal
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tugas_id',
        'bimtek_id',
        'user_id',
        'file_jawaban_path',
        'nilai',
        'feedback',
        'user_id_penilai',
    ];

    /**
     * Get the tugas that owns the pengumpulan.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * Get the user (peserta) who submitted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for user - the peserta.
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (PIC/Panitia) who graded.
     */
    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_penilai');
    }

    /**
     * Check if this submission has been graded.
     */
    public function isGraded(): bool
    {
        return $this->nilai !== null;
    }
}