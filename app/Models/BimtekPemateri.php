<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BimtekPemateri extends Model
{
    use HasFactory;

    protected $table = 'bimtek_pemateris';

    protected $fillable = [
        'bimtek_id',
        'nama_pemateri',
        'asal_instansi',
    ];

    /**
     * Relasi balik ke kelas Bimtek Utama
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class, 'bimtek_id');
    }
}