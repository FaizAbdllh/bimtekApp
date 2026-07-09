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
        'pic_user_id',
        'judul_rencana',
        'tempat_kegiatan_rencana',
        'sumber_pembiayaan',
        'tanggal_mulai_rencana',
        'tanggal_selesai_rencana',
        'deskripsi_rencana',
        'jumlah_peserta',
        'jenis_kegiatan',
        'judul_final',
        'lokasi_aktual',
        'tanggal_mulai_aktual',
        'tanggal_selesai_aktual',
        'mode_pelaksanaan',
        'virtual_meeting_url',
        'invite_code',
        'anggaran_disetujui',
        'deskripsi_jadwal',
        'daftar_pemateri',
        'file_surat_undangan_path',
        'file_surat_undangan_uploaded_by',
        'file_surat_undangan_uploaded_at',
        'butuh_verifikasi_dokumen',
        'has_tugas',
        'has_sertifikat',
        'syarat_kehadiran_persen',
        'syarat_tugas_persen',
        'syarat_tugas_wajib',
        'status',
        'catatan_kepala',
        'kepala_approved_at',
        'catatan_ppk',
        'ppk_approved_at',
        'status_rt',
        'catatan_rt',
    ];

    protected $casts = [
        'tanggal_mulai_rencana' => 'date',
        'tanggal_selesai_rencana' => 'date',
        'tanggal_mulai_aktual' => 'date',
        'tanggal_selesai_aktual' => 'date',
        'anggaran_disetujui' => 'decimal:2',
        'daftar_pemateri' => 'array',
        'butuh_verifikasi_dokumen' => 'boolean',
        'has_tugas' => 'boolean',
        'has_sertifikat' => 'boolean',
        'syarat_tugas_wajib' => 'boolean',
        'file_surat_undangan_uploaded_at' => 'datetime',
        'kepala_approved_at' => 'datetime',
        'ppk_approved_at' => 'datetime',
    ];

    public function getInviteLinkAttribute(): ?string
    {
        if (! $this->invite_code) {
            return null;
        }
        return url("/bimtek/{$this->id}/daftar?code={$this->invite_code}");
    }

    /**
     * Relasi ke Aktor Pembuat Kegiatan (PIC)
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    /**
     * Relasi ke Petugas Upload Surat Undangan
     */
    public function undanganUploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'file_surat_undangan_uploaded_by');
    }

    /**
     * Relasi Jembatan Panitia (Sesuai tabel terpisah baru)
     */
    public function panitia(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_panitias', 'bimtek_id', 'user_id')
            ->withPivot('fungsi_panitia')
            ->withTimestamps();
    }

    /**
     * Relasi Jembatan Peserta (Sesuai tabel terpisah baru)
     */
    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimtek_pesertas', 'bimtek_id', 'user_id')
            ->withPivot('status_verifikasi')
            ->withTimestamps();
    }
    /**
     * Mengambil daftar narasumber / pemateri yang bertugas di kelas Bimtek ini
     */
    public function pemateris(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BimtekPemateri::class, 'bimtek_id');
    }
    public function kebutuhanAnggarans(): HasMany
    {
        return $this->hasMany(KebutuhanAnggaran::class, 'bimtek_id');
    }

    public function fasilitasLogistiks(): HasMany
    {
        return $this->hasMany(FasilitasLogistik::class, 'bimtek_id');
    }

    public function materis(): HasMany
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    public function sesiAbsensis(): HasMany
    {
        return $this->hasMany(SesiAbsensi::class);
    }

    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    /**
     * Polimorfisme Logistik / Kondisi Pengajuan Dinamis
     */
    public function isInternal(): bool { return $this->jenis_kegiatan === 'internal'; }
    public function isEksternal(): bool { return $this->jenis_kegiatan === 'eksternal'; }

    public function getTanggalMulaiFinalAttribute() { return $this->tanggal_mulai_aktual ?? $this->tanggal_mulai_rencana; }
    public function getTanggalSelesaiFinalAttribute() { return $this->tanggal_selesai_aktual ?? $this->tanggal_selesai_rencana; }
    public function getLokasiFinalAttribute(): ?string { return $this->lokasi_aktual ?? $this->tempat_kegiatan_rencana; }

    public function getModePelaksanaanLabelAttribute(): string
    {
        return match ($this->mode_pelaksanaan ?? 'offline') {
            'online' => 'Online',
            'hybrid' => 'Hybrid',
            default => 'Offline',
        };
    }
}