<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
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
        return $this->belongsTo(Tugas::class);
    }

    /**
     * Get the user (peserta) who submitted.
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
