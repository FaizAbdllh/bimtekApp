<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tugas';

    protected $fillable = [
        'bimtek_id',
        'judul',
        'deskripsi',
        'file_instruksi_path',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Get the bimtek that owns the tugas.
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class);
    }

    /**
     * Get all pengumpulan for this tugas.
     */
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    /**
     * Check if deadline has passed.
     */
    public function isDeadlinePassed(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }
}
