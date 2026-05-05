# Perbandingan Perancangan vs Implementasi
## Sistem Informasi Bimbingan Teknis (SI Bimtek) BBPMP Sumbar

**Tanggal Dokumentasi:** 14 Januari 2026

---

## 1. PERBANDINGAN STRUKTUR DATABASE

### A. Tabel pada Perancangan Awal (13 Tabel)

| No | Nama Tabel | Keterangan |
|----|------------|------------|
| 1 | users | Data pengguna sistem |
| 2 | roles | Data peran/role |
| 3 | pengajuans | Data pengajuan bimtek |
| 4 | bimteks | Data bimtek yang disetujui |
| 5 | bimtek_user | Relasi user dengan bimtek (pivot) |
| 6 | kebutuhan_anggarans | Data kebutuhan anggaran |
| 7 | materis | Data materi pembelajaran |
| 8 | tugas | Data tugas peserta |
| 9 | pengumpulan_tugas | Data pengumpulan jawaban tugas |
| 10 | sesi_absensis | Data sesi absensi |
| 11 | absensi_pesertas | Data kehadiran peserta |
| 12 | sertifikats | Data sertifikat peserta |
| 13 | template_sertifikats | Data template sertifikat |

### B. Tabel pada Implementasi Akhir (16 Tabel Utama)

*Note: Tidak termasuk tabel Laravel default (cache, jobs, job_batches, failed_jobs, sessions, password_reset_tokens)*

| No | Nama Tabel | Status | Keterangan Perubahan |
|----|------------|--------|---------------------|
| 1 | users | ✅ Sesuai | Ditambah field `email_verified_at` |
| 2 | roles | ✅ Sesuai | - |
| 3 | pengajuans | ✅ Diperluas | + `status_rt`, `catatan_rt`, `status_draft` |
| 4 | bimteks | ✅ Diperluas | + `syarat_kehadiran_persen`, `syarat_tugas_persen`, `syarat_tugas_wajib`, `daftar_pemateri` (JSON) |
| 5 | bimtek_user | ✅ Sesuai | Enum: `pic`, `panitia`, `peserta` |
| 6 | kebutuhan_anggarans | ✅ Sesuai | - |
| 7 | materis | ✅ Sesuai | - |
| 8 | tugas | ✅ Sesuai | - |
| 9 | pengumpulan_tugas | ✅ Sesuai | - |
| 10 | sesi_absensis | ✅ Sesuai | - |
| 11 | absensi_pesertas | ✅ Sesuai | - |
| 12 | sertifikats | ✅ Sesuai | - |
| 13 | template_sertifikats | ✅ Sesuai | - |
| 14 | **log_sistems** | ➕ BARU | Logging aktivitas sistem |
| 15 | **fasilitas_logistiks** | ➕ BARU | Kebutuhan fasilitas untuk Koordinator RT |
| 16 | migrations | Laravel | Tracking migration history |

### C. Ringkasan Perubahan Database

| Aspek | Perancangan | Implementasi | Selisih |
|-------|-------------|--------------|---------|
| Jumlah Tabel Utama | 13 | 15 | +2 tabel baru |
| Field Tambahan | - | 9 field | +9 field |
| Index Performa | 0 | 6 | +6 index |

---

## 2. PERBANDINGAN AKTOR/ROLE

### A. Role pada Perancangan Awal (6 Role)

| No | Role | Fungsi |
|----|------|--------|
| 1 | Admin IT | Manajemen user & sistem |
| 2 | Kepala | Approval pengajuan tahap 1 |
| 3 | PPK | Approval anggaran tahap 2 |
| 4 | Koordinator RT | Pemenuhan fasilitas |
| 5 | Pegawai Internal | Pengajuan & pelaksanaan bimtek |
| 6 | Peserta Eksternal | Peserta dari luar instansi |

### B. Role pada Implementasi Akhir (6 Role)

| No | Role | Fungsi | Status |
|----|------|--------|--------|
| 1 | Admin IT | Manajemen user, template sertifikat, **log sistem** | ✅ Diperluas |
| 2 | Kepala | Approval pengajuan tahap 1 | ✅ Sesuai |
| 3 | PPK | Approval anggaran tahap 2 | ✅ Sesuai |
| 4 | Koordinator RT | Pemenuhan fasilitas **& logistik** | ✅ Diperluas |
| 5 | Pegawai Internal | Pengajuan, PIC, Panitia, **Peserta** | ✅ Sesuai |
| 6 | Peserta Eksternal | Peserta dari luar instansi | ✅ Sesuai |

### C. Perubahan Akses Role

| Role | Perancangan | Implementasi | Perubahan |
|------|-------------|--------------|-----------|
| Admin IT | User Management | + Template Sertifikat, + Log Sistem | +2 modul |
| Koordinator RT | Fasilitas saja | + Field logistik pada fasilitas | +detail logistik |
| Pegawai Internal | PIC, Panitia, Peserta | Sesuai perancangan | - |

---

## 3. PERBANDINGAN FITUR & FUNGSIONAL

### A. Modul pada Perancangan Awal (12 Modul)

| No | Modul | Deskripsi |
|----|-------|-----------|
| 1 | Authentication | Login, logout, reset password |
| 2 | Dashboard | Statistik per role |
| 3 | Manajemen User | CRUD user (Admin IT) |
| 4 | Pengajuan Bimtek | Create, edit, delete pengajuan |
| 5 | Approval Kepala | Review & approve pengajuan |
| 6 | Approval PPK | Review & approve anggaran |
| 7 | Koordinator RT | Pemenuhan fasilitas |
| 8 | Kelola Bimtek | Manajemen bimtek aktif |
| 9 | Materi | Upload & kelola materi |
| 10 | Tugas | Kelola & nilai tugas |
| 11 | Absensi | Kelola kehadiran peserta |
| 12 | Sertifikat | Generate sertifikat |

### B. Modul pada Implementasi Akhir (15 Modul)

| No | Modul | Status | Fitur Tambahan |
|----|-------|--------|----------------|
| 1 | Authentication | ✅ Diperluas | + Email verification, + Password reset via email |
| 2 | Dashboard | ✅ Diperluas | + Dashboard per role dengan statistik spesifik |
| 3 | Manajemen User | ✅ Diperluas | + Reset password by admin, + Filter & pagination |
| 4 | Pengajuan Bimtek | ✅ Diperluas | + Draft mode, + Kebutuhan anggaran dinamis |
| 5 | Approval Kepala | ✅ Sesuai | + Catatan approval |
| 6 | Approval PPK | ✅ Sesuai | + Catatan approval |
| 7 | Koordinator RT | ✅ Diperluas | + Fasilitas logistik detail, + Status pemenuhan |
| 8 | Kelola Bimtek | ✅ Diperluas | + Assign PIC/Panitia/Peserta, + Input data pemateri, + Download undangan |
| 9 | Materi | ✅ Diperluas | + Preview file, + Multi kategori |
| 10 | Tugas | ✅ Diperluas | + Preview jawaban, + Deadline management |
| 11 | Absensi | ✅ Diperluas | + Multiple sesi, + Absensi mandiri/manual, + Rekap otomatis |
| 12 | Sertifikat | ✅ Diperluas | + Template dinamis, + Syarat kelulusan configurable |
| 13 | **Template Sertifikat** | ➕ BARU | Kelola template sertifikat (Admin IT) |
| 14 | **Laporan** | ➕ BARU | Export PDF & Excel untuk berbagai rekap |
| 15 | **Log Sistem** | ➕ BARU | Monitoring aktivitas sistem (Admin IT) |

### C. Ringkasan Perubahan Fitur

| Aspek | Perancangan | Implementasi | Selisih |
|-------|-------------|--------------|---------|
| Jumlah Modul | 12 | 15 | +3 modul baru |
| Total Routes | ~80 | 125 | +45 routes |
| Views (Blade) | ~40 | 60+ | +20 views |
| Controllers | 10 | 16 | +6 controllers |

---

## 4. PERBANDINGAN ALUR PROSES BISNIS

### A. Pembagian Proses

| Perancangan Awal | Implementasi |
|------------------|--------------|
| 3 Proses: Pengajuan, Persiapan, Pelaksanaan | 2 Proses: **Pengajuan** & **Pelaksanaan** |

**Alasan perubahan:** 
- Batas antara "persiapan" dan "pelaksanaan" tidak jelas dalam praktik
- Setelah pengajuan disetujui, semua aktivitas adalah bagian dari "pelaksanaan bimtek"
- RT dapat bekerja **paralel** dengan PIC/Panitia (tidak perlu menunggu)

### B. Alur Pengajuan Bimtek

**Perancangan Awal (BPMN):**
```
PIC → Isi Formulir → Kepala Review → [Setuju/Tolak/Revisi]
                                          │
                                          ▼ (jika setuju)
                                     PPK Review → [Setuju/Revisi]
                                          │
                                          ▼ (jika revisi PPK)
                                     Kembali ke PIC
```

**Implementasi:**
```
Pegawai Internal → Draft (opsional) → Submit Pengajuan 
    → Kepala Review [Setuju/Tolak/Revisi]
        → (jika revisi) Kembali ke Pengaju
        → (jika setuju) PPK Review Anggaran [Setuju/Tolak/Revisi]
            → (jika revisi) Kembali ke Pengaju
            → (jika setuju) Disetujui Final → Bimtek Dibuat
```

**Status:** ✅ Sesuai dengan BPMN, termasuk:
- Kepala dapat minta revisi
- PPK dapat minta revisi (baru ditambahkan)

### C. Alur Pelaksanaan Bimtek

**Perancangan Awal (BPMN):** 
- RT memproses kebutuhan **setelah** PIC assign panitia (sequential)

**Implementasi:**
- RT memproses kebutuhan **paralel** dengan aktivitas PIC/Panitia

```
Bimtek Disetujui 
    │
    ├──► [PARALEL] RT: Memproses fasilitas & logistik
    │
    └──► PIC: Assign Panitia & Peserta
              │
              └──► Panitia: Upload Materi, Buat Tugas
                        │
                        └──► [Saat Kegiatan] Buka Sesi Absensi
                                    │
                                    └──► Rekap & Generate Sertifikat
```

**Status:** ✅ RT paralel lebih efisien (disetujui sebagai improvement)

### D. Kontrol Absensi

**Perancangan:** Gating berdasarkan status "berlangsung"

**Implementasi:** Kontrol di level **SESI ABSENSI**
- Panitia membuat sesi → status default "ditutup"
- Saat kegiatan dimulai → Panitia buka sesi
- Peserta bisa absen **hanya jika sesi terbuka**
- Setelah selesai → Panitia tutup sesi

**Status:** ✅ Lebih fleksibel dan praktis (sesi = kontrol per hari/per sesi kegiatan)

### E. Alur Kelayakan Sertifikat

**Perancangan Awal:**
```
Kehadiran >= 80% → Sertifikat
```

**Implementasi:**
```
Syarat Configurable per Bimtek:
- syarat_kehadiran_persen (default 80%)
- syarat_tugas_persen (default 80%)
- syarat_tugas_wajib (true/false)

Eligible = (Kehadiran >= syarat_kehadiran) AND 
           (Tugas >= syarat_tugas OR !syarat_tugas_wajib)
```

**Status:** ✅ Lebih fleksibel, dapat dikonfigurasi per bimtek

---

## 5. PERBANDINGAN TEKNIS

### A. Teknologi

| Aspek | Perancangan | Implementasi |
|-------|-------------|--------------|
| Framework | Laravel 11 | Laravel 12.x |
| PHP | 8.2+ | 8.2.28 |
| Database | MySQL | MySQL |
| Frontend | Blade + Tailwind | Blade + Tailwind CSS |
| PDF | DomPDF | barryvdh/laravel-dompdf v3.1.1 |
| Email | SMTP | Gmail SMTP (configured) |

### B. Optimasi Performa (Tidak di Perancangan)

| Optimasi | Keterangan |
|----------|------------|
| Database Indexes | 6 index pada kolom yang sering di-query |
| N+1 Query Fix | Pre-loading data untuk menghindari N+1 |
| Single Query Stats | Menggabungkan multiple COUNT menjadi 1 query |
| View Caching | Laravel view caching enabled |

### C. Keamanan (Tidak di Perancangan)

| Fitur Keamanan | Implementasi |
|----------------|--------------|
| Role-based Middleware | ✅ Implemented |
| CSRF Protection | ✅ Laravel default |
| Password Hashing | ✅ bcrypt |
| File Upload Validation | ✅ Type & size validation |
| Authorization per Route | ✅ Controller-level |

---

## 6. FITUR TAMBAHAN (TIDAK ADA DI PERANCANGAN)

### A. Fitur Baru yang Ditambahkan

| No | Fitur | Deskripsi | Alasan Penambahan |
|----|-------|-----------|-------------------|
| 1 | Log Sistem | Mencatat semua aktivitas penting | Audit trail & debugging |
| 2 | Template Sertifikat | Kelola template dinamis | Fleksibilitas desain sertifikat |
| 3 | Laporan Export | PDF & Excel export | Kebutuhan dokumentasi |
| 4 | Draft Pengajuan | Simpan pengajuan sebagai draft | UX improvement |
| 5 | Preview File | Preview materi & jawaban | UX improvement |
| 6 | Syarat Sertifikat Dinamis | Configurable per bimtek | Fleksibilitas aturan |
| 7 | Multiple Absensi Sessions | Banyak sesi per bimtek | Kebutuhan real |
| 8 | Data Pemateri | Field JSON untuk dokumentasi/laporan | Kebutuhan dokumentasi |
| 9 | Fasilitas Logistik | Detail kebutuhan RT | Kebutuhan real |
| 10 | Flash Messages | Notifikasi aksi user | UX improvement |

### B. Fitur Enhancement dari Perancangan

| Fitur Original | Enhancement |
|----------------|-------------|
| User Management | + Reset password by admin |
| Pengajuan | + Draft mode, + Multi anggaran |
| Absensi | + Multiple sessions, + Rekap otomatis |
| Sertifikat | + Syarat configurable, + Template dinamis |
| Dashboard | + Statistik per role |

---

## 7. RINGKASAN PERBANDINGAN

### A. Statistik Perbandingan

| Metrik | Perancangan | Implementasi | Perubahan |
|--------|-------------|--------------|-----------|
| Tabel Database | 13 | 15 | +2 tabel baru |
| Role/Aktor | 6 | 6 | 0 (sama) |
| Modul Utama | 12 | 15 | +3 modul baru |
| Routes | ~80 | 125 | +45 routes |
| Controllers | 10 | 16 | +6 controllers |
| Views | ~40 | 60+ | +20 views |
| Models | 13 | 14 | +1 model |

*Catatan: Angka tidak termasuk file bawaan Laravel (cache, jobs, sessions, dll)*

### B. Kesimpulan Penyesuaian

1. **Database:** Struktur dasar sesuai perancangan dengan penambahan 2 tabel baru (log_sistems, fasilitas_logistiks) dan beberapa field tambahan untuk fleksibilitas.

2. **Aktor/Role:** Jumlah role tetap 6 sesuai perancangan. Pegawai Internal dapat berperan sebagai PIC, Panitia, atau Peserta dalam konteks bimtek tertentu. Pemateri bukan pengguna sistem, hanya disimpan sebagai data untuk dokumentasi/laporan.

3. **Fitur:** 12 modul perancangan terimplementasi penuh dengan enhancement, ditambah 3 modul baru (Template Sertifikat, Laporan, Log Sistem).

4. **Alur Proses:** 
   - Proses disederhanakan dari 3 fase (Pengajuan, Persiapan, Pelaksanaan) menjadi 2 fase (Pengajuan, Pelaksanaan)
   - PPK dapat minta revisi ke pengaju (sesuai BPMN)
   - RT bekerja paralel dengan PIC/Panitia (improvement dari BPMN)
   - Kontrol absensi di level sesi (lebih fleksibel dari status bimtek)

5. **Teknis:** Upgrade ke Laravel 12, optimasi performa query, dan penambahan fitur keamanan.

---

## 8. DAFTAR FITUR PER ROLE (FINAL)

### Admin IT
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Manajemen User | Tambah, edit, hapus, dan lihat data user sistem |
| Reset Password User | Mengatur ulang password user yang lupa |
| Manajemen Template Sertifikat | Tambah, edit, hapus template sertifikat (file gambar) |
| Log Sistem | Melihat riwayat aktivitas sistem, filter, dan hapus log |
| Dashboard | Melihat statistik total user, bimtek, dan pengajuan |

### Kepala
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Dashboard | Melihat jumlah pengajuan menunggu, disetujui, dan ditolak |
| Approval Pengajuan | Memeriksa detail pengajuan, lalu memutuskan: setujui, tolak, atau minta revisi |
| Catatan Approval | Menuliskan catatan/alasan saat menyetujui, menolak, atau meminta revisi |

### PPK
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Dashboard | Melihat jumlah pengajuan menunggu persetujuan anggaran |
| Approval Anggaran | Memeriksa RAB, lalu memutuskan: setujui, tolak, atau minta revisi |
| Lihat RAB | Melihat tabel rincian anggaran biaya (uraian, volume, harga, total) |
| Catatan Approval | Menuliskan catatan/alasan saat menyetujui, menolak, atau meminta revisi |

### Koordinator RT
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Dashboard | Melihat jumlah kebutuhan fasilitas yang belum/sudah dipenuhi |
| Daftar Pengajuan | Melihat daftar pengajuan yang sudah disetujui final |
| Kelola Fasilitas | Melihat detail kebutuhan fasilitas & logistik per pengajuan |
| Update Status | Mengubah status pemenuhan: belum/sebagian/telah dipenuhi |

### Pegawai Internal (sebagai Pengaju/PIC/Panitia)
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Dashboard | Melihat jumlah pengajuan milik sendiri dan bimtek yang diikuti |
| Buat Pengajuan | Mengisi formulir pengajuan bimtek (draft atau langsung submit) |
| Edit Pengajuan | Mengubah data pengajuan yang masih draft atau perlu revisi |
| Assign Panitia | Menambahkan pegawai internal lain sebagai panitia bimtek |
| Undang Peserta | Menambahkan peserta (internal/eksternal) ke bimtek |
| Input Pemateri | Mengisi data pemateri (nama, asal instansi) untuk dokumentasi |
| Upload Materi | Mengunggah file materi/panduan untuk peserta |
| Buat Tugas | Membuat tugas dengan deskripsi, deadline, dan file pendukung |
| Nilai Tugas | Memberikan nilai dan catatan pada jawaban tugas peserta |
| Buka Sesi Absensi | Membuat dan membuka sesi absensi untuk peserta |
| Input Kehadiran Manual | Menambahkan kehadiran peserta yang lupa absen |
| Generate Sertifikat | Membuat sertifikat untuk peserta yang memenuhi syarat |
| Export Laporan | Mengunduh rekap kehadiran, nilai, dan sertifikat (PDF/Excel) |
| Ikut Bimtek Lain | Berpartisipasi sebagai peserta di bimtek yang diikuti |

### Peserta Eksternal
| Fitur | Kegiatan yang Dilakukan |
|-------|------------------------|
| Dashboard | Melihat daftar bimtek yang diikuti dan sertifikat diperoleh |
| Lihat Bimtek | Melihat detail bimtek yang sedang/telah diikuti |
| Akses Materi | Mengunduh file materi/panduan yang disediakan panitia |
| Submit Tugas | Mengunggah jawaban tugas sebelum deadline |
| Absensi | Mengklik tombol "Hadir" saat sesi absensi dibuka |
| Download Sertifikat | Mengunduh sertifikat jika sudah memenuhi syarat kelulusan |

---

*Dokumen ini dibuat untuk dokumentasi perbandingan antara perancangan awal dengan implementasi sistem SI Bimtek BBPMP Sumbar.*
