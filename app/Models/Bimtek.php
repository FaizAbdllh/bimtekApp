<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        
        // ✅ PERBAIKAN: Mengarah ke Pintu Masuk / ActivationController yang benar
        return route('register.invite', ['code' => $this->invite_code]);
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
     * Relasi ke tabel syarat_dokumens (One-to-Many)
     */
    public function syaratDokumens(): HasMany
    {
        // Sesuaikan 'SyaratDokumen' dengan nama Class Model milik tabel syarat_dokumens Anda
        return $this->hasMany(SyaratDokumen::class, 'bimtek_id');
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

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class,'bimtek_id');
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
    /**
     * Cek apakah Bimtek masih dalam fase bisa diedit (Materi, Tugas, Info Utama)
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['persiapan', 'registrasi', 'persiapan_selesai']);
    }

    /**
     * Cek apakah Bimtek sedang dalam fase registrasi/pendaftaran
     */
    public function isRegistrationOpen(): bool
    {
        return $this->status === 'registrasi';
    }

    /**
     * Cek apakah Bimtek sedang berjalan
     */
    public function isRunning(): bool
    {
        return $this->status === 'berlangsung';
    }

    /**
     * Cek apakah Bimtek sudah dikunci permanen (Selesai/Batal)
     */
    public function isLocked(): bool
    {
        return in_array($this->status, ['selesai', 'dibatalkan']);
    }
    
    public function canOpenRegistration(): array
    {
        $errors = [];

        // 1. Status saat ini benar-benar `persiapan`
        if ($this->status !== 'persiapan') {$errors[] = 'Bimtek harus berada dalam fase Persiapan untuk dapat membuka registrasi.';
        }

        // 2. PIC sudah ditetapkan dan masih aktif
        if (empty($this->pic_user_id) || !$this->pic || !$this->pic->is_active) {$errors[] = 'PIC kegiatan belum ditetapkan, atau akun PIC yang bersangkutan saat ini tidak aktif.';
        }

        // 3. PIC sudah menambahkan minimal satu panitia
        if (!$this->panitia()->exists()) {$errors[] = 'Minimal satu orang panitia harus ditambahkan ke dalam kegiatan ini.';
        }

        // 4. Validasi Mode Pelaksanaan
        $validModes = ['offline', 'online', 'hybrid'];
        if (empty($this->mode_pelaksanaan) || !in_array($this->mode_pelaksanaan, $validModes)) {$errors[] = 'Mode pelaksanaan tidak valid atau belum diisi. Nilai yang diizinkan: ' . implode(', ', $validModes) . '.';
        }

        // 5. Validasi Jenis Kegiatan
        $validJenis = ['internal', 'eksternal'];
        if (empty($this->jenis_kegiatan) || !in_array($this->jenis_kegiatan, $validJenis)) {$errors[] = 'Jenis kegiatan tidak valid atau belum dipilih. Nilai yang diizinkan: ' . implode(', ', $validJenis) . '.';
        }

        // 6, 7, 8, 9. Validasi Kelengkapan Data Teks Dasar lainnya
        if (empty($this->judul_rencana)) {$errors[] = 'Judul rencana kegiatan belum diisi.';
        }
        if (empty($this->deskripsi_rencana)) {$errors[] = 'Deskripsi kegiatan belum diisi.';
        }
        if (empty($this->tempat_kegiatan_rencana)) {$errors[] = 'Tempat/lokasi rencana belum ditetapkan.';
        }
        if (empty($this->sumber_pembiayaan)) {$errors[] = 'Sumber pembiayaan belum ditentukan.';
        }

        // 10. Kuota peserta terisi dan lebih dari nol
        if (empty($this->jumlah_peserta) || $this->jumlah_peserta <= 0) {$errors[] = 'Kuota peserta wajib diisi dengan angka lebih dari nol.';
        }

        // 11. Tanggal mulai dan selesai terisi serta logis
        if (empty($this->tanggal_mulai_rencana) || empty($this->tanggal_selesai_rencana)) {$errors[] = 'Tanggal mulai dan selesai rencana pelaksanaan belum lengkap.';
        } elseif ($this->tanggal_selesai_rencana->isBefore($this->tanggal_mulai_rencana)) {$errors[] = 'Logika jadwal keliru: Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        }

        // 12. Jika verifikasi aktif, syarat dokumen wajib ada
        if ($this->butuh_verifikasi_dokumen && !$this->syaratDokumens()->exists()) {$errors[] = 'Fitur verifikasi dokumen diaktifkan, namun belum ada jenis dokumen persyaratan yang ditambahkan.';
        }

        // 13. URL virtual meeting
        if (in_array($this->mode_pelaksanaan, ['online', 'hybrid']) && empty($this->virtual_meeting_url)) {$errors[] = 'Pelaksanaan berstatus Online/Hybrid mewajibkan ketersediaan tautan (URL) virtual meeting.';
        }

        // 14. Surat Undangan sudah diunggah
        if (empty($this->file_surat_undangan_path)) {$errors[] = 'Dokumen surat undangan resmi belum diunggah.';
        } elseif (!Storage::disk('public')->exists($this->file_surat_undangan_path)) {$errors[] = 'File fisik surat undangan tidak ditemukan di server (kemungkinan terhapus/korup). Harap unggah ulang dokumen.';
        }

        return [
            'allowed' => empty($errors),
            'errors'  => $errors
        ];
    }

    /**
     * Guard State Machine: Validasi transisi dari 'registrasi' -> 'persiapan_selesai'
     */
    public function canCompletePreparation(): array
    {
        $errors = [];
        
        // 1. Status saat ini wajib `registrasi`
        if ($this->status !== 'registrasi') {$errors[] = 'Bimtek harus berada dalam fase Registrasi untuk dapat diselesaikan persiapannya.';
        }

        // 2. Minimal satu panitia masih terdaftar
        if (!$this->panitia()->exists()) {$errors[] = 'Minimal satu orang panitia harus tetap terdaftar dalam kegiatan ini.';
        }

        // 3 & 4. Validasi Data Peserta & Verifikasi Dokumen
        $jumlahPeserta =$this->peserta()->count();
        if ($jumlahPeserta === 0) {$errors[] = 'Belum ada peserta yang mendaftar. Kelas tidak dapat dilanjutkan ke tahap persiapan selesai tanpa peserta.';
        }

        if ($this->butuh_verifikasi_dokumen) {
            // Memastikan tidak ada peserta yang status verifikasinya masih menggantung ('menunggu'/'pending')
            // (Silakan sesuaikan string 'menunggu' dengan default status di database Anda)
            $pesertaMenggantung =$this->peserta()->wherePivotIn('status_verifikasi', ['menunggu', 'pending'])->exists();
            if ($pesertaMenggantung) {$errors[] = 'Masih terdapat dokumen peserta yang belum diperiksa panitia. Loloskan atau tolak sisa pendaftar terlebih dahulu.';
            }

            // Memastikan ada minimal 1 peserta yang lolos (kelas tidak kosong setelah disaring)
            $pesertaLolos =$this->peserta()->wherePivotIn('status_verifikasi', ['verified', 'diverifikasi', 'disetujui'])->count();
            if ($pesertaLolos === 0 && $jumlahPeserta > 0) {$errors[] = 'Tidak ada satupun peserta yang lolos verifikasi dokumen.';
            }
        }

        // 5. Jika fitur tugas aktif, tugas wajib sudah disiapkan
        if ($this->has_tugas && !$this->tugas()->exists()) {$errors[] = 'Fitur tugas kelas diaktifkan, namun draf lembar tugas belum dibuat ke dalam sistem.';
        }

        // 6. Pemateri dan Materi sudah tersedia (Bisa disesuaikan jika opsional)
        if (!$this->pemateris()->exists()) {$errors[] = 'Daftar narasumber/pemateri belum ditambahkan.';
        }
        if (!$this->materis()->exists()) {$errors[] = 'Modul materi kegiatan belum diunggah.';
        }

        // 7 & 10. Jadwal dan Data Aktual Lengkap
        if (empty($this->tanggal_mulai_aktual) || empty($this->tanggal_selesai_aktual)) {$errors[] = 'Jadwal aktual pelaksanaan (tanggal mulai & selesai) wajib ditetapkan secara final sebelum pendaftaran ditutup.';
        }
        if (empty($this->lokasi_aktual) && in_array($this->mode_pelaksanaan, ['offline', 'hybrid'])) {$errors[] = 'Lokasi aktual pelaksanaan belum ditetapkan untuk kegiatan tatap muka.';
        }

        // 8 & 9. Fasilitas/Logistik dan Persetujuan RT
        if ($this->fasilitasLogistiks()->exists() && $this->status_rt !== 'telah_dipenuhi') {$errors[] = 'Terdapat permohonan fasilitas/logistik yang diajukan, namun belum disetujui atau dipenuhi oleh Koordinator RT.';
        }

        return [
            'allowed' => empty($errors), 
            'errors'  => $errors
        ];
    }

    /**
     * Guard State Machine: Validasi transisi dari 'persiapan_selesai' -> 'berlangsung'
     */
    public function canStartEvent(): array
    {
        $errors = [];
        
        // 1 & 9. Status saat ini wajib `persiapan_selesai`
        if ($this->status !== 'persiapan_selesai') {$errors[] = 'Persiapan Bimtek belum dinyatakan selesai.';
        }

        // 2, 3, & 4. Validasi Tanggal Aktual
        if (empty($this->tanggal_mulai_aktual) || empty($this->tanggal_selesai_aktual)) {$errors[] = 'Tanggal mulai dan selesai aktual belum ditetapkan.';
        } elseif (\Carbon\Carbon::parse($this->tanggal_mulai_aktual)->isAfter(\Carbon\Carbon::parse($this->tanggal_selesai_aktual))) {$errors[] = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.';
        } elseif (\Carbon\Carbon::parse($this->tanggal_mulai_aktual)->isFuture()) {$errors[] = 'Tanggal mulai aktual belum tiba.';
        }

        // 5. Modul Presensi (Disesuaikan dengan enum 'terbuka' pada tabel sesi_absensis)
        if (!$this->sesiAbsensis()->where('status', 'terbuka')->exists()) {$errors[] = 'Minimal harus ada satu sesi absensi dengan status terbuka sebelum kegiatan dimulai.';
        }

        // 6. Pemateri sudah tersedia
        if (!$this->pemateris()->exists()) {$errors[] = 'Daftar narasumber/pemateri final belum tersedia.';
        }

        // 7. Panitia tetap tersedia
        if (!$this->panitia()->exists()) {$errors[] = 'Tidak ada panitia yang terdaftar untuk mengawal kegiatan ini.';
        }

        // 8. Link meeting tersedia untuk online/hybrid
        if (in_array($this->mode_pelaksanaan, ['online', 'hybrid']) && empty($this->virtual_meeting_url)) {$errors[] = 'Tautan (URL) virtual meeting wajib diisi untuk pelaksanaan berstatus Online atau Hybrid.';
        }

        return [
            'allowed' => empty($errors), 
            'errors'  => $errors
        ];
    }

    /**
     * Guard State Machine: Validasi transisi dari 'berlangsung' -> 'selesai'
     */
    public function canFinishEvent(): array
    {
        $errors = [];

        // 1. Status saat ini wajib `berlangsung`
        if ($this->status !== 'berlangsung') {$errors[] = 'Bimtek harus berstatus Berlangsung untuk dapat diakhiri.';
        }

        // 2 & 3. Tanggal selesai aktual sudah terisi dan sudah berlalu
        if (empty($this->tanggal_selesai_aktual)) {$errors[] = 'Tanggal selesai aktual belum ditetapkan.';
        } elseif (\Carbon\Carbon::parse($this->tanggal_selesai_aktual)->isFuture()) {$errors[] = 'Kegiatan belum bisa diakhiri karena tanggal selesai aktual belum terlewati.';
        }

        // 4. Seluruh sesi absensi sudah ditutup
        if ($this->sesiAbsensis()->where('status', 'terbuka')->exists()) {$errors[] = 'Masih ada sesi absensi yang berstatus terbuka. Harap tutup semua sesi absensi terlebih dahulu.';
        }

        // 6. Tugas peserta sudah dinilai
        // Menggunakan relasi langsung ke tabel pengumpulan_tugas berdasarkan bimtek_id
        if ($this->has_tugas) {
            if ($this->pengumpulanTugas()->whereNull('nilai')->exists()) {$errors[] = 'Fitur tugas aktif, namun masih terdapat hasil pengumpulan tugas peserta yang belum dinilai.';
            }
        }

        // 7. Persyaratan sertifikat sudah diproses (Validasi Menyeluruh)
        if ($this->has_sertifikat) {
            // Hitung total peserta yang sah/terverifikasi
            $jumlahPesertaAktif = $this->peserta()->wherePivot('status_verifikasi', 'verified')->count();
            
            // Hitung total sertifikat yang sudah tercetak untuk kelas ini
            $jumlahSertifikat = $this->sertifikats()->count();

            if ($jumlahSertifikat === 0 && $jumlahPesertaAktif > 0) {
                $errors[] = 'Fitur sertifikat diaktifkan, namun belum ada sertifikat yang diterbitkan. Harap proses generate kelulusan/sertifikat terlebih dahulu.';
            } elseif ($jumlahSertifikat < $jumlahPesertaAktif) {
                $selisih = $jumlahPesertaAktif - $jumlahSertifikat;
                $errors[] = "Masih terdapat {$selisih} peserta terverifikasi yang belum diproses sertifikatnya.";
            }
        }

        return [
            'allowed' => empty($errors),
            'errors'  => $errors
        ];
    }

    /**
     * Guard State Machine: Validasi transisi ke status 'dibatalkan'
     */
    public function canCancel(): array
    {
        $errors = [];

        // 1. Status saat ini belum `selesai`
        if ($this->status === 'selesai') {$errors[] = 'Kegiatan yang sudah diselesaikan secara penuh tidak dapat dibatalkan.';
        }
        
        // 2. Cegah pembatalan ganda
        if ($this->status === 'dibatalkan') {$errors[] = 'Kegiatan ini sudah berstatus dibatalkan.';
        }

        return [
            'allowed' => empty($errors),
            'errors'  => $errors
        ];
    }
}