<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'materis';

    protected $fillable = [
        'bimtek_id',
        'judul',
        'file_path',
        'tipe',
    ];

    /**
     * Get the bimtek that owns the materi.
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class);
    }

    /**
     * Check if this is materi type.
     */
    public function isMateri(): bool
    {
        return $this->tipe === 'materi';
    }

    /**
     * Check if this is panduan type.
     */
    public function isPanduan(): bool
    {
        return $this->tipe === 'panduan';
    }
}
