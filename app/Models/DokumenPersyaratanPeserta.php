<?php

namespace App\Models;

// HAPUS trait HasUuids dari import
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class DokumenPersyaratanPeserta extends Model
{
    // 💡 1. JANGAN GUNAKAN HasUuids ATAU HasUlids DI SINI

    protected $table = 'dokumen_persyaratan_peserta';

    // 💡 2. MATIKAN DEFAULT PRIMARY KEY LARAVEL ('id')
    protected $primaryKey = null;
    public $incrementing = false;

    // 💡 3. BANTU LARAVEL MENGENALI COMPOSITE KEY SAAT MELAKUKAN UPDATE
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('bimtek_id', $this->getAttribute('bimtek_id'))
                     ->where('user_id', $this->getAttribute('user_id'))
                     ->where('syarat_dokumen_id', $this->getAttribute('syarat_dokumen_id'));
    }

    // 💡 4. SESUAIKAN DENGAN NAMA KOLOM DI DATABASE (syarat_dokumen_id)
    protected $fillable = [
        'syarat_dokumen_id', // <-- Ubah dari jenis_dokumen menjadi syarat_dokumen_id
        'bimtek_id',
        'user_id',
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
        return $this->belongsTo(Bimtek::class, 'bimtek_id');
    }

    /**
     * Get the user (peserta) that uploaded the dokumen.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Syarat Dokumen Induk (WAJIB DITAMBAHKAN)
     */
    public function syaratDokumen(): BelongsTo
    {
        return $this->belongsTo(SyaratDokumen::class, 'syarat_dokumen_id');
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