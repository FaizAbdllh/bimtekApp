<?php

namespace App\Providers;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerGates();
    }

    /**
     * Register all gates for authorization
     */
    protected function registerGates(): void
    {
        // =====================
        // GATE UNTUK ROLE SISTEM
        // =====================

        // Admin IT - full access
        Gate::define('admin-it', function (User $user) {
            return $user->isAdminIt();
        });

        // Kepala - approval pengajuan
        Gate::define('kepala', function (User $user) {
            return $user->isKepala();
        });

        // PPK - approval anggaran
        Gate::define('ppk', function (User $user) {
            return $user->isPpk();
        });

        // Koordinator RT - pemenuhan kebutuhan
        Gate::define('koordinator-rt', function (User $user) {
            return $user->isRt();
        });

        // Pegawai Internal - bisa mengajukan bimtek
        Gate::define('pegawai-internal', function (User $user) {
            return $user->isPegawaiInternal();
        });

        // Peserta Eksternal
        Gate::define('peserta-eksternal', function (User $user) {
            return $user->isPesertaEksternal();
        });

        // =====================
        // GATE UNTUK PENGAJUAN
        // =====================

        // Bisa membuat pengajuan baru (hanya Pegawai Internal)
        Gate::define('create-pengajuan', function (User $user) {
            return $user->isPegawaiInternal();
        });

        // Bisa melihat semua pengajuan (Admin IT, Kepala, PPK, RT)
        Gate::define('view-all-pengajuan', function (User $user) {
            return $user->isAdminIt() || $user->isKepala() || $user->isPpk() || $user->isRt();
        });

        // Bisa approve pengajuan level 1 (Kepala)
        Gate::define('approve-pengajuan-kepala', function (User $user) {
            return $user->isKepala();
        });

        // Bisa approve pengajuan level 2 (PPK)
        Gate::define('approve-pengajuan-ppk', function (User $user) {
            return $user->isPpk();
        });

        // Bisa update status RT
        Gate::define('update-status-rt', function (User $user) {
            return $user->isRt();
        });

        // =====================
        // GATE UNTUK BIMTEK
        // =====================

        // Bisa melihat semua bimtek (Admin IT)
        Gate::define('view-all-bimtek', function (User $user) {
            return $user->isAdminIt();
        });

        // Bisa kelola bimtek (PIC atau Panitia dalam bimtek tersebut)
        Gate::define('manage-bimtek', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // Bisa melihat detail bimtek (peserta bimtek atau pengelola)
        Gate::define('view-bimtek', function (User $user, Bimtek $bimtek) {
            if ($user->isAdminIt()) {
                return true;
            }
            $peran = $user->getPeranKontekstual($bimtek->id);
            return $peran !== null;
        });

        // =====================
        // GATE UNTUK MATERI
        // =====================

        // Bisa upload materi (PIC atau Panitia)
        Gate::define('upload-materi', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // Bisa download materi (semua peserta bimtek)
        Gate::define('download-materi', function (User $user, Bimtek $bimtek) {
            if ($user->isAdminIt()) {
                return true;
            }
            $peran = $user->getPeranKontekstual($bimtek->id);
            return $peran !== null;
        });

        // =====================
        // GATE UNTUK TUGAS
        // =====================

        // Bisa membuat tugas (PIC atau Panitia)
        Gate::define('create-tugas', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // Bisa mengumpulkan tugas (Peserta)
        Gate::define('submit-tugas', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return $peran === 'peserta';
        });

        // Bisa menilai tugas (PIC atau Panitia)
        Gate::define('grade-tugas', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // =====================
        // GATE UNTUK ABSENSI
        // =====================

        // Bisa membuat sesi absensi (PIC atau Panitia)
        Gate::define('create-absensi', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // Bisa mengisi absensi (Peserta)
        Gate::define('fill-absensi', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return $peran === 'peserta';
        });

        // =====================
        // GATE UNTUK SERTIFIKAT
        // =====================

        // Bisa kelola template sertifikat (Admin IT)
        Gate::define('manage-template-sertifikat', function (User $user) {
            return $user->isAdminIt();
        });

        // Bisa download sertifikat (Peserta yang eligible)
        Gate::define('download-sertifikat', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return $peran === 'peserta';
        });

        // =====================
        // GATE UNTUK USER MANAGEMENT
        // =====================

        // Bisa kelola user (Admin IT)
        Gate::define('manage-users', function (User $user) {
            return $user->isAdminIt();
        });

        // =====================
        // GATE UNTUK ANGGARAN
        // =====================

        // Bisa input kebutuhan anggaran (PIC atau Panitia)
        Gate::define('input-anggaran', function (User $user, Bimtek $bimtek) {
            $peran = $user->getPeranKontekstual($bimtek->id);
            return in_array($peran, ['pic', 'panitia']);
        });

        // Bisa approve anggaran (PPK)
        Gate::define('approve-anggaran', function (User $user) {
            return $user->isPpk();
        });
    }
}
