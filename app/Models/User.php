<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'asal_instansi',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the global role of the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * 1. RELASI PIC: Menampilkan semua bimtek di mana user ini ditunjuk sebagai PIC utama.
     * (Logika baru: PIC tertanam langsung di tabel induk bimteks)
     */
    public function bimteksSebagaiPic(): HasMany
    {
        return $this->hasMany(Bimtek::class, 'pic_id'); // Pastikan kolom di tabel bimteks bernama pic_id atau user_id
    }

    /**
     * 2. RELASI PANITIA: Menghubungkan user ke Bimtek melalui tabel jembatan baru `bimtek_panitias`
     */
    public function bimteksSebagaiPanitia(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_panitias', 'user_id', 'bimtek_id')
                    ->withPivot('fungsi_panitia') // Sesuai kolom di migrasi bimtek_panitias
                    ->withTimestamps();
    }

    /**
     * 3. RELASI PESERTA: Menghubungkan user ke Bimtek melalui tabel jembatan baru `bimtek_pesertas`
     */
    public function bimteksSebagaiPeserta(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_pesertas', 'user_id', 'bimtek_id')
                    ->withPivot('status_verifikasi') // Kolom berkah tersembunyi yang kita bahas tadi!
                    ->withTimestamps();
    }

    /**
     * Relasi Transaksional Turunan (Menyesuaikan tabel anak bertipe Composite Key)
     */
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'user_id');
    }

    public function absensiPesertas(): HasMany
    {
        return $this->hasMany(AbsensiPeserta::class, 'user_id');
    }

    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'user_id');
    }

    public function logSistems(): HasMany
    {
        return $this->hasMany(LogSistem::class, 'user_id');
    }

    public function dokumenPersyaratan(): HasMany
    {
        return $this->hasMany(DokumenPersyaratanPeserta::class, 'user_id');
    }

    /**
     * Global Role Check Helpers
     */
    public function hasRole(string|array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }

        $roles = is_array($roleNames) ? $roleNames : [$roleNames];
        return in_array($this->role->nama_peran, $roles);
    }

    public function isAdminIt(): bool { return $this->hasRole('Admin IT'); }
    public function isKepala(): bool { return $this->hasRole('Kepala'); }
    public function isPpk(): bool { return $this->hasRole('PPK'); }
    public function isRt(): bool { return $this->hasRole('Koordinator RT'); }
    public function isPersuratan(): bool { return $this->hasRole('Persuratan'); }
    public function isPegawaiInternal(): bool { return $this->hasRole('Pegawai Internal'); }
    public function isPesertaEksternal(): bool { return $this->hasRole('Peserta Eksternal'); }

    /**
     * Contextual Role Check Helpers (Menyesuaikan Arsitektur Terpisah)
     */
    public function isPicDiBimtek(Bimtek $bimtek): bool
    {
        return $this->bimteksSebagaiPic()->where('id', $bimtek->id)->exists();
    }

    public function isPanitiaDiBimtek(Bimtek $bimtek): bool
    {
        return $this->bimteksSebagaiPanitia()->where('bimtek_id', $bimtek->id)->exists();
    }

    public function isPesertaDiBimtek(Bimtek $bimtek): bool
    {
        return $this->bimteksSebagaiPeserta()->where('bimtek_id', $bimtek->id)->exists();
    }
}