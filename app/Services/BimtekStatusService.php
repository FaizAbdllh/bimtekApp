<?php

namespace App\Services;

use App\Models\Bimtek;

class BimtekStatusService
{
    /**
     * Memeriksa daftar kelengkapan syarat sebelum registrasi bisa dibuka.
     */
    public function checkReadiness(Bimtek $bimtek): array
    {
        // 1. Definisikan daftar syarat dan logika pengecekannya
        $syarat = [
            'jadwal_diisi' => !is_null($bimtek->tanggal_mulai_rencana) && !is_null($bimtek->tanggal_selesai_rencana),
            'kuota_ditentukan' => $bimtek->jumlah_peserta > 0,
            // Asumsi: Anda memiliki relasi dokumenSurat() atau field file_surat untuk mengecek surat undangan
            'surat_diunggah' => !is_null($bimtek->file_surat), 
            // Asumsi: Mengecek apakah daftar syarat dokumen untuk peserta (jika eksternal) sudah diatur
            'syarat_peserta_diatur' => $bimtek->jenis_kegiatan === 'internal' || 
                                      ($bimtek->jenis_kegiatan === 'eksternal' && $bimtek->syaratDokumens()->exists()),
        ];

        // 2. Tentukan apakah SEMUA syarat sudah terpenuhi (bernilai true)
        $isReady = !in_array(false, $syarat, true);

        // 3. Kembalikan data untuk dikonsumsi oleh tampilan Blade
        return [
            'is_ready' => $isReady,
            'checklist' => $syarat
        ];
    }
}