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
        'bimtek_id', // Mengarah langsung ke tabel induk kegiatan baru
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
     * Relasi ke tabel induk Bimtek
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class, 'bimtek_id');
    }
}