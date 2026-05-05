<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FasilitasLogistik extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fasilitas_logistiks';

    protected $fillable = [
        'pengajuan_id',
        'nama_fasilitas',
        'jumlah',
        'satuan',
        'is_dipenuhi',
        'status',
    ];

    protected $casts = [
        'is_dipenuhi' => 'boolean',
    ];

    /**
     * Get the pengajuan that owns the fasilitas.
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
