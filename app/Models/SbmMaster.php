<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SbmMaster extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sbm_masters';

    protected $fillable = [
        'kode_sbm',
        'nama_item',
        'harga_satuan',
        'satuan_primary',
        'satuan_secondary',
        'kategori',
        'tahun_berlaku',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'tahun_berlaku' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get kebutuhan anggarans yang menggunakan SBM ini.
     */
    public function kebutuhanAnggarans(): HasMany
    {
        return $this->hasMany(KebutuhanAnggaran::class);
    }

    /**
     * Scope untuk filter SBM yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan kategori.
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan tahun berlaku.
     */
    public function scopeTahunBerlaku($query, $tahun)
    {
        return $query->where('tahun_berlaku', $tahun);
    }

    /**
     * Get display name dengan harga.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->nama_item} - Rp" . number_format($this->harga_satuan, 0, ',', '.');
    }

    /**
     * Format harga untuk display.
     */
    public function formatHarga(): string
    {
        return "Rp" . number_format($this->harga_satuan, 0, ',', '.');
    }
}
