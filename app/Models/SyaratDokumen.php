<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids; // 💡 1. Import Trait ULID
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyaratDokumen extends Model
{
    use HasUlids; // 💡 2. Aktifkan pembuat ID unik otomatis untuk kolom 'id'

    // Mengunci nama tabel fisik agar sinkron dengan MySQL Anda
    protected $table = 'syarat_dokumens';

    // Daftarkan kolom yang diizinkan untuk operasi Mass Assignment
    protected $fillable = [
        'bimtek_id',
        'nama_dokumen',
        'deskripsi_syarat',
        'is_wajib',
    ];

    // Konversi otomatis nilai kolom 1/0 dari database menjadi true/false di PHP
    protected $casts = [
        'is_wajib' => 'boolean',
    ];

    /**
     * Relasi balik ke tabel induk Bimtek (Many-to-One)
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class, 'bimtek_id');
    }

    /**
     * Relasi ke bukti fisik file yang diunggah oleh peserta (One-to-Many)
     */
    public function dokumenPesertas(): HasMany
    {
        return $this->hasMany(DokumenPersyaratanPeserta::class, 'syarat_dokumen_id');
    }
}