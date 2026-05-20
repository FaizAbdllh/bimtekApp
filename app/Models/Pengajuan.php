<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengajuans';

    protected $fillable = [
        'user_id',
        'judul_rencana',
        'tempat_kegiatan',
        'sumber_pembiayaan',
        'tanggal_mulai_rencana',
        'tanggal_selesai_rencana',
        'deskripsi_rencana',
        'mode_pelaksanaan',
        'jumlah_peserta',
        'jenis_kegiatan',
        'status_pengajuan',
        'is_draft',
        'catatan_kepala',
        'kepala_approved_at',
        'catatan_ppk',
        'status_rt',
        'catatan_rt',
        'catatan_logistik',
        'butuh_verifikasi_dokumen',
        'jenis_dokumen_wajib',
    ];

    protected $casts = [
        'tanggal_mulai_rencana' => 'date',
        'tanggal_selesai_rencana' => 'date',
        'mode_pelaksanaan' => 'string',
        'jumlah_peserta' => 'integer',
        'is_draft' => 'boolean',
        'kepala_approved_at' => 'datetime',
        'butuh_verifikasi_dokumen' => 'boolean',
        'jenis_dokumen_wajib' => 'array',
    ];

    /**
     * Get the user who created the pengajuan (PIC).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - the PIC who submitted.
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the bimtek created from this pengajuan.
     */
    public function bimtek(): HasOne
    {
        return $this->hasOne(Bimtek::class);
    }

    /**
     * Get the fasilitas logistik for the pengajuan.
     */
    public function fasilitasLogistiks(): HasMany
    {
        return $this->hasMany(FasilitasLogistik::class);
    }

    /**
     * Get the kebutuhan anggaran (RAB) for the pengajuan.
     */
    public function kebutuhanAnggarans(): HasMany
    {
        return $this->hasMany(KebutuhanAnggaran::class);
    }

    /**
     * Check if pengajuan is internal.
     */
    public function isInternal(): bool
    {
        return $this->jenis_kegiatan === 'internal';
    }

    /**
     * Check if pengajuan is eksternal.
     */
    public function isEksternal(): bool
    {
        return $this->jenis_kegiatan === 'eksternal';
    }

    /**
     * Get mode pelaksanaan label.
     */
    public function getModePelaksanaanLabelAttribute(): string
    {
        return match ($this->mode_pelaksanaan ?? 'offline') {
            'online' => 'Online',
            'hybrid' => 'Hybrid',
            default => 'Offline',
        };
    }
}
