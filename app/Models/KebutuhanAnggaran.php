<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KebutuhanAnggaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kebutuhan_anggarans';

    protected $fillable = [
        'bimtek_id', // Mengarah langsung ke tabel induk kegiatan baru
        'sbm_master_id',
        'nama_item',
        'volume_1',
        'satuan_1',
        'volume_2',
        'satuan_2',
        'harga_satuan',
        'harga_satuan_sbm',
        'total_biaya',
        'kategori',
        'status_validasi',
        'persentase_deviasi',
        'justifikasi_deviasi',
        'approved_by',
        'approved_at',
    ];

    protected $appends = [
        'satuan_primary',
        'satuan_secondary',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'harga_satuan_sbm' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'persentase_deviasi' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel induk Bimtek
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class, 'bimtek_id');
    }

    public function sbmMaster(): BelongsTo
    {
        return $this->belongsTo(SbmMaster::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Otomatisasi kalkulasi total biaya & deviasi SBM
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Kalkulasi total biaya secara dinamis
            $model->total_biaya = $model->volume_1 * $model->volume_2 * $model->harga_satuan;

            // Kalkulasi deviasi terhadap pagu atas SBM Master 2026
            if ($model->harga_satuan_sbm && $model->harga_satuan_sbm > 0) {
                $deviasi = (($model->harga_satuan - $model->harga_satuan_sbm) / $model->harga_satuan_sbm) * 100;
                $model->persentase_deviasi = round($deviasi, 2);

                if (abs($deviasi) < 0.01) {
                    $model->status_validasi = 'sesuai_sbm';
                } elseif (abs($deviasi) < 10) {
                    $model->status_validasi = 'deviasi_minor';
                } elseif (abs($deviasi) < 20) {
                    $model->status_validasi = 'deviasi_major';
                } else {
                    $model->status_validasi = 'deviasi_signifikan';
                }
            } else {
                $model->status_validasi = 'non_sbm';
                $model->persentase_deviasi = null;
            }
        });
    }

    public function needsApproval(): bool
    {
        return in_array($this->status_validasi, ['deviasi_major', 'deviasi_signifikan']);
    }

    public function getSatuanPrimaryAttribute() { return $this->satuan_1; }
    public function setSatuanPrimaryAttribute($value) { $this->satuan_1 = $value; }
    public function getSatuanSecondaryAttribute() { return $this->satuan_2; }
    public function setSatuanSecondaryAttribute($value) { $this->satuan_2 = $value; }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status_validasi) {
            'sesuai_sbm' => 'bg-green-100 text-green-800',
            'deviasi_minor' => 'bg-yellow-100 text-yellow-800',
            'deviasi_major' => 'bg-orange-100 text-orange-800',
            'deviasi_signifikan' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_validasi) {
            'sesuai_sbm' => 'Sesuai SBM',
            'deviasi_minor' => 'Deviasi Minor (<10%)',
            'deviasi_major' => 'Deviasi Major (10-20%)',
            'deviasi_signifikan' => 'Deviasi Signifikan (>20%)',
            'non_sbm' => 'Non-SBM',
            default => 'Unknown',
        };
    }
}