<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sesi_absensis';

    protected $fillable = [
        'bimtek_id',
        'nama_sesi',
        'status',
        'user_id',
        'qr_code',
        'qr_generated_at',
        'qr_expires_at',
    ];

    protected $casts = [
        'qr_generated_at' => 'datetime',
        'qr_expires_at' => 'datetime',
    ];

    /**
     * Get the bimtek that owns the sesi absensi.
     */
    public function bimtek(): BelongsTo
    {
        return $this->belongsTo(Bimtek::class);
    }

    /**
     * Get the user (PIC/Panitia) who opened this session.
     */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all absensi peserta for this session.
     */
    public function absensiPesertas(): HasMany
    {
        return $this->hasMany(AbsensiPeserta::class);
    }

    /**
     * Alias for absensiPesertas relationship.
     */
    public function absensis(): HasMany
    {
        return $this->absensiPesertas();
    }

    /**
     * Check if session is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'terbuka';
    }

    /**
     * Check if session is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'ditutup';
    }

    /**
     * Generate new QR code for this session.
     * QR is valid for the entire duration of the session.
     * Expires when session is closed.
     */
    public function generateQrCode(): void
    {
        // Generate unique QR data: session_id|timestamp|random_token
        $qrData = base64_encode($this->id.'|'.now()->timestamp.'|'.bin2hex(random_bytes(8)));

        $this->update([
            'qr_code' => $qrData,
            'qr_generated_at' => now(),
            'qr_expires_at' => null, // No expiry - valid until session closes
        ]);
    }

    /**
     * Check if QR code is valid (not expired and matches).
     * QR is valid as long as session is open and code matches.
     */
    public function isQrCodeValid(string $scannedQr): bool
    {
        // Check if QR exists
        if (! $this->qr_code) {
            return false;
        }

        // Check if QR matches
        if ($this->qr_code !== $scannedQr) {
            return false;
        }

        // Check if session is open (QR only valid while session is open)
        if (! $this->isOpen()) {
            return false;
        }

        return true;
    }

    /**
     * Check if QR code has expired.
     * QR is only valid when session is open.
     */
    public function isQrExpired(): bool
    {
        // QR is expired if session is closed
        return $this->isClosed();
    }
}
