<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bimtek extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bimteks';

    protected $fillable = [
        'pengajuan_id',
        'pic_user_id',
        'judul_final',
        'tanggal_mulai_aktual',
        'tanggal_selesai_aktual',
        'lokasi_aktual',
        'anggaran_disetujui',
        'deskripsi_jadwal',
        'daftar_pemateri',
        'file_surat_undangan_path',
        'status_pelaksanaan',
        'syarat_kehadiran_persen',
        'syarat_tugas_persen',
        'syarat_tugas_wajib',
        'butuh_verifikasi_dokumen',
        'jenis_dokumen_wajib',
    ];

    protected $casts = [
        'tanggal_mulai_aktual' => 'date',
        'tanggal_selesai_aktual' => 'date',
        'anggaran_disetujui' => 'decimal:2',
        'syarat_tugas_wajib' => 'boolean',
        'daftar_pemateri' => 'array',
        'butuh_verifikasi_dokumen' => 'boolean',
        'jenis_dokumen_wajib' => 'array',
    ];

    /**
     * Get the pengajuan that owns the bimtek.
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    /**
     * Get all users (Panitia, Peserta) for this bimtek.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_user')
            ->withPivot(['peran_kontekstual', 'fungsi_panitia', 'status_verifikasi', 'notified_at'])
            ->withTimestamps();
    }

    /**
     * Get PIC (Penanggung Jawab) for this bimtek.
     * PIC is the owner of the bimtek, typically the pengaju.
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    /**
     * Get Panitia for this bimtek.
     */
    public function panitia(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_user')
            ->withPivot(['peran_kontekstual', 'fungsi_panitia'])
            ->wherePivot('peran_kontekstual', 'panitia')
            ->withTimestamps();
    }

    /**
     * Get Peserta for this bimtek.
     */
    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'fungsi_panitia', 'status_verifikasi', 'notified_at')
            ->wherePivot('peran_kontekstual', 'peserta')
            ->withTimestamps();
    }

    /**
     * Get all dokumen persyaratan for this bimtek.
     */
    public function dokumenPersyaratan(): HasMany
    {
        return $this->hasMany(DokumenPersyaratanPeserta::class);
    }

    /**
     * Get peserta yang perlu verifikasi dokumen.
     */
    public function pesertaPendingVerifikasi(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'status_verifikasi', 'notified_at')
            ->wherePivot('peran_kontekstual', 'peserta')
            ->wherePivot('status_verifikasi', 'pending')
            ->withTimestamps();
    }

    /**
     * Get peserta yang sudah verified.
     */
    public function pesertaVerified(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'status_verifikasi', 'notified_at')
            ->wherePivot('peran_kontekstual', 'peserta')
            ->wherePivot('status_verifikasi', 'verified')
            ->withTimestamps();
    }

    /**
     * Get daftar pemateri (JSON field).
     * Pemateri bukan user sistem, hanya data informasi untuk dokumentasi/laporan.
     * Format: [{"nama": "...", "asal_instansi": "...", "bidang": "..."}]
     */
    public function getDaftarPemateriArrayAttribute(): array
    {
        return $this->daftar_pemateri ?? [];
    }

    /**
     * Get all materis for this bimtek.
     */
    public function materis(): HasMany
    {
        return $this->hasMany(Materi::class);
    }

    /**
     * Get materi type materi only.
     */
    public function materiPembelajaran(): HasMany
    {
        return $this->hasMany(Materi::class)->where('tipe', 'materi');
    }

    /**
     * Get materi type panduan only.
     */
    public function panduan(): HasMany
    {
        return $this->hasMany(Materi::class)->where('tipe', 'panduan');
    }

    /**
     * Get all tugas for this bimtek.
     */
    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    /**
     * Get all sesi absensi for this bimtek.
     */
    public function sesiAbsensis(): HasMany
    {
        return $this->hasMany(SesiAbsensi::class);
    }

    /**
     * Get all sertifikats for this bimtek.
     */
    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    /**
     * Check if bimtek is internal.
     */
    public function isInternal(): bool
    {
        return $this->pengajuan->isInternal();
    }

    /**
     * Check if bimtek is eksternal.
     */
    public function isEksternal(): bool
    {
        return $this->pengajuan->isEksternal();
    }

    /**
     * Get jenis kegiatan from pengajuan.
     */
    public function getJenisKegiatanAttribute(): string
    {
        return $this->pengajuan->jenis_kegiatan;
    }

    /**
     * Get final start date (aktual or from pengajuan).
     */
    public function getTanggalMulaiFinalAttribute()
    {
        return $this->tanggal_mulai_aktual ?? $this->pengajuan?->tanggal_mulai;
    }

    /**
     * Get final end date (aktual or from pengajuan).
     */
    public function getTanggalSelesaiFinalAttribute()
    {
        return $this->tanggal_selesai_aktual ?? $this->pengajuan?->tanggal_selesai;
    }

    /**
     * Get final location (aktual or from pengajuan).
     */
    public function getLokasiFinalAttribute(): ?string
    {
        return $this->lokasi_aktual ?? $this->pengajuan?->lokasi;
    }

    /**
     * Get narasumber (pemateri) for this bimtek - first one.
     */
    public function getNarasumberAttribute()
    {
        // Pemateri disimpan sebagai array pada field daftar_pemateri
        if (is_array($this->daftar_pemateri) && count($this->daftar_pemateri) > 0) {
            return $this->daftar_pemateri[0];
        }
        return null;
    }

    /**
     * Get status.
     */
    public function getStatusAttribute(): string
    {
        return $this->status_pelaksanaan ?? 'persiapan';
    }
}
