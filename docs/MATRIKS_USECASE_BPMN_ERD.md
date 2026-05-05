# Matriks Use Case, BPMN, Fungsional Aktor, dan ERD
## SI Bimtek BBPMP Sumbar (Implementasi Aktual)

Tanggal pembaruan: 01 April 2026

---

## 1. Aktor dan Fungsional Utama

| No | Aktor | Jenis | Fungsional Utama |
|---|---|---|---|
| 1 | Admin IT | Role sistem | Kelola user, monitoring log sistem, akses laporan lintas bimtek |
| 2 | Kepala | Role sistem | Review pengajuan tahap 1 (setuju/tolak/revisi), akses laporan |
| 3 | PPK | Role sistem | Review pengajuan tahap 2 (setuju/tolak/revisi), finalisasi pengajuan, akses laporan |
| 4 | Koordinator RT | Role sistem | Pemenuhan fasilitas/logistik pengajuan yang sudah disetujui final |
| 5 | Pegawai Internal (Pengaju) | Role sistem | Buat/ubah/ajukan pengajuan bimtek, revisi pengajuan |
| 6 | PIC | Peran kontekstual bimtek | Kelola pelaksanaan bimtek, assign panitia dari Pegawai Internal, atur status, undangan |
| 7 | Panitia | Peran kontekstual bimtek | Kelola materi, tugas, absensi, verifikasi dokumen, sertifikat; jabatan panitia dicatat sebagai penanggung jawab/ketua/sekretaris/anggota/lainnya |
| 8 | Peserta Internal | Peran kontekstual bimtek | Akses materi/tugas/absensi/sertifikat sesuai aturan verifikasi |
| 9 | Peserta Eksternal | Role sistem + peran kontekstual | Mengikuti bimtek sebagai peserta, upload dokumen, akses pembelajaran |

---

## 2. Matriks Use Case per Aktor

| Aktor | Use Case |
|---|---|
| Admin IT | Login, kelola user (CRUD/reset password), lihat log sistem, filter/export log, akses laporan (rekap peserta/absensi/nilai/kegiatan, export CSV/Excel/PDF) |
| Kepala | Login, review pengajuan level 1, beri catatan revisi/penolakan, akses laporan |
| PPK | Login, review pengajuan level 2, beri catatan revisi/penolakan, setujui final (trigger pembuatan bimtek), akses laporan |
| Koordinator RT | Lihat pengajuan disetujui final, isi/update kebutuhan fasilitas-logistik, update status pemenuhan |
| Pegawai Internal (Pengaju) | Buat draft pengajuan, submit pengajuan, edit pengajuan, ajukan ulang setelah revisi |
| PIC | Lihat detail bimtek, update status pelaksanaan, assign panitia dari Pegawai Internal, kelola peserta, upload/preview/download undangan, ajukan revisi ke pengajuan |
| Panitia | Upload/edit/hapus materi, buat/edit/hapus tugas, nilai tugas, buat sesi absensi, buka/tutup sesi, tambah kehadiran manual, verifikasi dokumen peserta, generate sertifikat, catat fungsi/jabatan panitia |
| Peserta Internal | Lihat bimtek yang diikuti, akses materi, kerjakan/submit tugas, scan QR absensi, lihat status verifikasi, unduh sertifikat jika memenuhi syarat |
| Peserta Eksternal | Login, upload dokumen persyaratan, akses pembelajaran seperti peserta, unduh sertifikat jika memenuhi syarat |

---

## 3. Matriks BPMN (Lane-by-Lane)

| Lane (Aktor) | Aktivitas Utama | Gateway/Keputusan | Output |
|---|---|---|---|
| Pegawai Internal (Pengaju) | Isi draft/submit pengajuan | Submit atau simpan draft | Data pengajuan masuk antrian approval |
| Kepala | Review pengajuan level 1 | Setuju / Tolak / Perlu Revisi | Status ke PPK atau kembali ke pengaju |
| PPK | Review pengajuan level 2 | Setuju / Tolak / Perlu Revisi | Jika setuju -> disetujui final |
| Sistem | Otomasi pasca persetujuan final | Pengajuan disetujui final? | Bimtek otomatis terbentuk + PIC terpasang |
| Koordinator RT | Proses fasilitas/logistik | Kebutuhan terpenuhi? | Status fasilitas/logistik terbarui |
| PIC | Kelola pelaksanaan bimtek | Panitia/ peserta sudah lengkap? | Pelaksanaan siap jalan |
| Panitia | Kelola materi dan tugas | Materi/tugas sudah dipublikasikan? | Peserta dapat mengikuti pembelajaran |
| Panitia | Kelola sesi absensi (buka/tutup) | Sesi dibuka? | Peserta bisa/tidak bisa absen |
| Peserta | Scan QR absensi dan submit tugas | Dokumen verified? | Kehadiran/tugas tercatat |
| Panitia/PIC | Verifikasi dokumen peserta | Dokumen lengkap/valid? | Status verifikasi peserta updated |
| Sistem | Evaluasi kelulusan sertifikat | Ambang kehadiran dan tugas terpenuhi? | Eligible/non-eligible sertifikat |
| Panitia | Generate sertifikat | Eligible? | File sertifikat PDF tersimpan/siap unduh |
| Admin IT/Kepala/PPK | Akses laporan | Jenis laporan dipilih | Export laporan PDF/CSV/Excel |

---

## 4. Matriks BPMN Objek Data

| Proses | Data Masuk | Data Keluar |
|---|---|---|
| Pengajuan | Form pengajuan + kebutuhan anggaran | Record pengajuans + kebutuhan_anggarans |
| Approval Kepala/PPK | Data pengajuan | status_pengajuan + catatan approval |
| Pembentukan Bimtek | Pengajuan disetujui final | Record bimteks + relasi PIC |
| Kelola Peserta | Data user/CSV import | Relasi bimtek_user (peserta/panitia + fungsi_panitia + status_verifikasi) |
| Verifikasi Dokumen | File dokumen persyaratan | dokumen_persyaratan_pesertas + status verifikasi |
| Pelaksanaan Pembelajaran | Materi, tugas, sesi absensi | materis, tugas, pengumpulan_tugas, absensi_pesertas |
| Sertifikasi | Rekap kehadiran + nilai tugas | sertifikats + file PDF |
| Pelaporan | Data bimtek operasional | Laporan PDF/CSV/Excel |

---

## 5. Matriks ERD Entitas Utama

| No | Entitas | PK | FK Utama | Keterangan |
|---|---|---|---|---|
| 1 | roles | id | - | Master role sistem |
| 2 | users | id | role_id -> roles.id | Data akun pengguna |
| 3 | pengajuans | id | user_id -> users.id | Pengajuan bimtek |
| 4 | kebutuhan_anggarans | id | pengajuan_id -> pengajuans.id | Rincian kebutuhan anggaran |
| 5 | fasilitas_logistiks | id | pengajuan_id -> pengajuans.id | Rincian fasilitas/logistik |
| 6 | bimteks | id | pengajuan_id -> pengajuans.id, pic_user_id -> users.id | Data bimtek hasil approval |
| 7 | bimtek_user | id | bimtek_id -> bimteks.id, user_id -> users.id | Relasi kontekstual panitia/peserta + fungsi panitia |
| 8 | dokumen_persyaratan_pesertas | id | bimtek_id -> bimteks.id, user_id -> users.id | Dokumen verifikasi peserta |
| 9 | materis | id | bimtek_id -> bimteks.id | Materi bimtek |
| 10 | tugas | id | bimtek_id -> bimteks.id | Tugas bimtek |
| 11 | pengumpulan_tugas | id | tugas_id -> tugas.id, user_id -> users.id | Jawaban dan penilaian tugas |
| 12 | sesi_absensis | id | bimtek_id -> bimteks.id | Sesi absensi |
| 13 | absensi_pesertas | id | sesi_absensi_id -> sesi_absensis.id, user_id -> users.id | Kehadiran peserta |
| 14 | sertifikats | id | bimtek_id -> bimteks.id, user_id -> users.id | Data sertifikat peserta |
| 15 | log_sistems | id | user_id -> users.id | Audit trail aktivitas |
| 16 | sbm_masters | id | - | Referensi SBM validasi anggaran |

---

## 6. Matriks Kardinalitas ERD

 

---

## 7. Kolom Kunci Implementasi (Perubahan Penting)

| Entitas | Kolom Kunci |
|---|---|
| bimteks | pic_user_id, syarat_kehadiran_persen, syarat_tugas_persen, syarat_tugas_wajib, daftar_pemateri, butuh_verifikasi_dokumen, jenis_dokumen_wajib |
| bimtek_user | peran_kontekstual (panitia/peserta), fungsi_panitia, status_verifikasi, notified_at |
| pengajuans | status_pengajuan, status_rt, status_draft, catatan_kepala, catatan_ppk |
| kebutuhan_anggarans | kategori_sbm, status_validasi_sbm, deviasi_persen, justifikasi_deviasi |
| sesi_absensis | status, qr_code, qr_generated_at, qr_expired_at |
| absensi_pesertas | metode_absen, waktu_absen |
| pengumpulan_tugas | nilai, feedback, user_id_penilai |
| sertifikats | nomor_sertifikat, file_path, tanggal_terbit |
| dokumen_persyaratan_pesertas | jenis_dokumen, status, verified_by, verified_at, catatan_verifikasi |

---

## 8. Catatan Scope Akses Laporan (Final)

| Role | Cakupan Laporan |
|---|---|
| Admin IT, Kepala, PPK | Lintas semua bimtek |
| PIC/Panitia (melalui Pegawai Internal) | Terbatas pada bimtek yang terlibat |
| Peserta | Tidak memiliki menu laporan |
