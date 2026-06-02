<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role of the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all pengajuans created by this user.
     */
    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    /**
     * Get all bimteks this user is involved in.
     */
    public function bimteks(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'fungsi_panitia')
            ->withTimestamps();
    }

    /**
     * Get bimteks where user is PIC.
     */
    public function bimteksSebagaiPic(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'fungsi_panitia')
            ->wherePivot('peran_kontekstual', 'pic')
            ->withTimestamps();
    }

    /**
     * Get bimteks where user is Panitia.
     */
    public function bimteksSebagaiPanitia(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'fungsi_panitia')
            ->wherePivot('peran_kontekstual', 'panitia')
            ->withTimestamps();
    }

    /**
     * Get bimteks where user is Peserta.
     */
    public function bimteksSebagaiPeserta(): BelongsToMany
    {
        return $this->belongsToMany(Bimtek::class, 'bimtek_user')
            ->withPivot('peran_kontekstual', 'fungsi_panitia')
            ->wherePivot('peran_kontekstual', 'peserta')
            ->withTimestamps();
    }

    /**
     * Get all pengumpulan tugas by this user.
     */
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    /**
     * Get all absensi by this user.
     */
    public function absensiPesertas(): HasMany
    {
        return $this->hasMany(AbsensiPeserta::class);
    }

    /**
     * Get all sertifikats for this user.
     */
    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    /**
     * Get all log sistem by this user.
     */
    public function logSistems(): HasMany
    {
        return $this->hasMany(LogSistem::class);
    }

    /**
     * Get all dokumen persyaratan uploaded by this user.
     */
    public function dokumenPersyaratan(): HasMany
    {
        return $this->hasMany(DokumenPersyaratanPeserta::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roleNames): bool
    {
        if (! $this->role) {
            return false;
        }

        $roles = is_array($roleNames) ? $roleNames : [$roleNames];

        return in_array($this->role->nama_peran, $roles);
    }

    /**
     * Check if user is Admin IT.
     */
    public function isAdminIt(): bool
    {
        return $this->hasRole('Admin IT');
    }

    /**
     * Check if user is Kepala.
     */
    public function isKepala(): bool
    {
        return $this->hasRole('Kepala');
    }

    /**
     * Check if user is PPK.
     */
    public function isPpk(): bool
    {
        return $this->hasRole('PPK');
    }

    /**
     * Check if user is RT (Koordinator Rumah Tangga).
     */
    public function isRt(): bool
    {
        return $this->hasRole('Koordinator RT');
    }

    /**
     * Check if user is Persuratan.
     */
    public function isPersuratan(): bool
    {
        return $this->hasRole('Persuratan');
    }

    /**
     * Check if user is Pegawai Internal.
     */
    public function isPegawaiInternal(): bool
    {
        return $this->hasRole('Pegawai Internal');
    }

    /**
     * Check if user is Peserta Eksternal.
     */
    public function isPesertaEksternal(): bool
    {
        return $this->hasRole('Peserta Eksternal');
    }

    /**
     * Get user's contextual role in a specific bimtek.
     */
    public function getPeranKontekstual(Bimtek $bimtek): ?string
    {
        $pivot = $this->bimteks()->where('bimtek_id', $bimtek->id)->first();

        return $pivot ? $pivot->pivot->peran_kontekstual : null;
    }

    /**
     * Check if user is PIC in a specific bimtek.
     */
    public function isPicDiBimtek(Bimtek $bimtek): bool
    {
        return $this->getPeranKontekstual($bimtek) === 'pic';
    }

    /**
     * Check if user is Panitia in a specific bimtek.
     */
    public function isPanitiaDiBimtek(Bimtek $bimtek): bool
    {
        return $this->getPeranKontekstual($bimtek) === 'panitia';
    }

    /**
     * Check if user is Peserta in a specific bimtek.
     */
    public function isPesertaDiBimtek(Bimtek $bimtek): bool
    {
        return $this->getPeranKontekstual($bimtek) === 'peserta';
    }
}
