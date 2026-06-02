<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DokumenPersyaratanPeserta extends Model
{
    use HasUuids;

    protected $table = 'dokumen_persyaratan_peserta';

    protected $fillable = [
        'bimtek_id',
        'user_id',
        'jenis_dokumen',
        'file_path',
        'file_name',
        'status',
        'uploaded_at',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the bimtek that owns the dokumen.
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class);
    }

    /**
     * Get the user (peserta) that uploaded the dokumen.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (panitia/pic) that verified the dokumen.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Check if dokumen is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if dokumen is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if dokumen is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
