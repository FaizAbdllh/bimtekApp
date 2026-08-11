# Manual Testing Guide - Fitur Verifikasi Dokumen

## Setup
Server sudah running di: http://127.0.0.1:8000

## Test Credentials (Password semua: **password**)

### Admin IT
- Email: admin@test.com
- Nama: Budi Santoso
- Menu: Manajemen User, Template Sertifikat, Log Sistem

### Kepala Satuan Kerja
- Email: kepala@test.com
- Nama: Dr. Hendra Wijaya
- Menu: Persetujuan Pengajuan Bimtek

### PPK (Pejabat Pembuat Komitmen)
- Email: ppk@test.com
- Nama: Rina Marlina, S.E.
- Menu: Persetujuan Anggaran

### Koordinator RT (Rumah Tangga)
- Email: rt@test.com
- Nama: Agus Setiawan
- Menu: Kebutuhan Fasilitas & Logistik

### Pegawai Internal
- Email: pegawai@test.com
- Nama: Dewi Kartika
- Menu: Ajukan Bimtek, Pengajuan Saya

### Peserta Eksternal
- Email: eksternal@test.com
- Nama: Muhammad Fauzi
- Menu: Bimtek yang diikuti, Aktivitas Saya

### PIC Bimtek (Untuk Testing)
- Email: pic@test.com
- Nama: Ahmad Rizki Pratama
- Menu: Ajukan Bimtek, Kelola Bimtek

### Peserta Test (Untuk Testing Verifikasi Dokumen)
- Email: peserta@test.com
- Nama: Siti Nurhaliza
- Menu: Kelola Bimtek

---

## Test Scenarios

### 1. Test Login & Akses Awal

#### 1.1 Login sebagai Peserta
- [ ] Buka http://127.0.0.1:8000/login
- [ ] Login dengan peserta@test.com / password
- [ ] Verify: Masuk ke dashboard
- [ ] Verify: Bisa lihat daftar bimtek yang diikuti

#### 1.2 Cek Status Verifikasi
- [ ] Lihat bimtek dengan verifikasi dokumen
- [ ] Verify: Status menunjukkan "invited" / belum upload dokumen
- [ ] Verify: Ada notifikasi/badge untuk upload dokumen

---

### 2. Test Upload Dokumen (Sebagai Peserta)

#### 2.1 Akses Form Upload
- [ ] Login sebagai peserta@test.com
- [ ] Klik bimtek yang memerlukan verifikasi
- [ ] Klik tombol "Upload Dokumen" atau sejenisnya
- [ ] Verify: Muncul form upload dokumen
- [ ] Verify: Menampilkan jenis dokumen yang wajib (Surat Tugas, SPPD)

#### 2.2 Upload Surat Tugas
- [ ] Pilih jenis dokumen: Surat Tugas
- [ ] Upload file PDF (max 2MB)
- [ ] Klik submit
- [ ] Verify: Success message muncul
- [ ] Verify: File muncul di daftar dokumen
- [ ] Verify: Status dokumen: "Pending"

#### 2.3 Upload SPPD
- [ ] Pilih jenis dokumen: SPPD
- [ ] Upload file PDF (max 2MB)
- [ ] Klik submit
- [ ] Verify: Success message muncul
- [ ] Verify: Status peserta berubah dari "invited" ke "pending"

#### 2.4 Test Validation
- [ ] Coba upload file > 2MB
- [ ] Verify: Error message muncul
- [ ] Coba upload file bukan PDF/JPG/PNG
- [ ] Verify: Error message muncul

---

### 3. Test Middleware - Akses Terblokir (Sebagai Peserta)

#### 3.1 Test Akses Fitur Sebelum Verified
- [ ] Login sebagai peserta@test.com (status: pending)
- [ ] Coba akses halaman Absensi
- [ ] Verify: Di-redirect ke halaman upload dokumen
- [ ] Coba akses halaman Tugas
- [ ] Verify: Di-redirect ke halaman upload dokumen
- [ ] Coba akses halaman Sertifikat
- [ ] Verify: Di-redirect ke halaman upload dokumen

---

### 4. Test Verifikasi Dokumen (Sebagai PIC)

#### 4.1 Login & Akses Dashboard Verifikasi
- [ ] Logout, login sebagai pic@test.com
- [ ] Masuk ke bimtek
- [ ] Klik menu "Verifikasi Dokumen" atau sejenisnya
- [ ] Verify: Muncul dashboard verifikasi
- [ ] Verify: Menampilkan daftar peserta
- [ ] Verify: Menampilkan dokumen yang sudah diupload

#### 4.2 Lihat Detail Dokumen
- [ ] Klik peserta test
- [ ] Verify: Menampilkan kedua dokumen (Surat Tugas, SPPD)
- [ ] Verify: Bisa download dokumen
- [ ] Klik download Surat Tugas
- [ ] Verify: File terdownload
- [ ] Klik download SPPD
- [ ] Verify: File terdownload

#### 4.3 Approve Dokumen Pertama (Surat Tugas)
- [ ] Klik tombol "Approve" di Surat Tugas
- [ ] (Optional) Isi catatan: "Dokumen sudah sesuai"
- [ ] Submit
- [ ] Verify: Success message
- [ ] Verify: Status dokumen berubah "Approved"
- [ ] Verify: Status peserta masih "pending" (karena SPPD belum approved)

#### 4.4 Approve Dokumen Kedua (SPPD)
- [ ] Klik tombol "Approve" di SPPD
- [ ] Submit
- [ ] Verify: Success message
- [ ] Verify: Status dokumen berubah "Approved"
- [ ] Verify: Status peserta berubah "Verified" ✓
- [ ] **Verify: Email terkirim ke peserta (cek log atau mailbox)**

---

### 5. Test Middleware - Akses Granted (Sebagai Peserta Verified)

#### 5.1 Test Akses Fitur Setelah Verified
- [ ] Logout, login kembali sebagai peserta@test.com
- [ ] Verify: Status menunjukkan "verified"
- [ ] Coba akses halaman Absensi
- [ ] Verify: Berhasil masuk (200 OK)
- [ ] Coba akses halaman Tugas
- [ ] Verify: Berhasil masuk (200 OK)
- [ ] Coba akses halaman Sertifikat
- [ ] Verify: Berhasil masuk (200 OK)

---

### 6. Test Reject Dokumen (Sebagai PIC)

#### 6.1 Setup: Upload Dokumen Baru
- [ ] Login sebagai peserta (buat peserta baru jika perlu)
- [ ] Upload Surat Tugas

#### 6.2 Reject Dokumen
- [ ] Login sebagai pic@test.com
- [ ] Masuk ke dashboard verifikasi
- [ ] Klik tombol "Reject" di dokumen
- [ ] Isi catatan: "Format tidak sesuai, mohon upload ulang"
- [ ] Submit
- [ ] Verify: Success message
- [ ] Verify: Status dokumen "Rejected"
- [ ] Verify: Status peserta "Rejected"
- [ ] **Verify: Email terkirim ke peserta dengan catatan penolakan**

#### 6.3 Test Reupload Dokumen Rejected
- [ ] Login sebagai peserta yang ditolak
- [ ] Verify: Bisa lihat dokumen yang ditolak
- [ ] Verify: Bisa lihat catatan penolakan
- [ ] Upload dokumen baru (yang sama jenis)
- [ ] Verify: Dokumen lama terhapus
- [ ] Verify: Dokumen baru muncul dengan status "Pending"
- [ ] Verify: Status peserta berubah ke "Pending"

---

### 7. Test Edge Cases

#### 7.1 Test Akses PIC/Panitia Bypass Middleware
- [ ] Login sebagai pic@test.com
- [ ] Coba akses Absensi/Tugas/Sertifikat
- [ ] Verify: Bisa akses tanpa verifikasi dokumen

#### 7.2 Test Bimtek Tanpa Verifikasi Dokumen
- [ ] Buat/Akses bimtek dengan `memerlukan_verifikasi_dokumen = false`
- [ ] Login sebagai peserta bimtek tersebut
- [ ] Coba akses Absensi/Tugas/Sertifikat
- [ ] Verify: Bisa akses langsung tanpa upload dokumen
---

### 8. Test Estimasi Jumlah Peserta & Pembatasan Panitia

#### 8.1 Estimasi Jumlah Peserta di Pengajuan
- [ ] Saat mengisi pengajuan, isi `Estimasi Jumlah Peserta` (angka). Ini wajib saat submit.
- [ ] Submit pengajuan dan verifikasi bahwa nilai tersimpan pada detail pengajuan.

#### 8.4 Ringkasan Kebutuhan (Telaah Staf)
- [ ] Saat mengisi pengajuan, isi kolom `Ringkasan Kebutuhan / Telaah Staf` dengan daftar kebutuhan singkat seperti: Transport, Uang Harian, Honor Panitia, Konsumsi, Penginapan, ATK.
- [ ] Submit pengajuan dan verifikasi bahwa ringkasan kebutuhan tersimpan dan tampil di halaman detail pengajuan.
- [ ] Verify: Tidak ada form RAB detail pada halaman pengajuan baru.

#### 8.2 Pembatasan Panitia (≤10% dari jumlah peserta)
- [ ] Masuk ke Bimtek yang memiliki estimasi peserta.
- [ ] Tambahkan Panitia melalui fitur Tambah Panitia sampai batas tercapai.
- [ ] Verify: Sistem menolak penambahan panitia jika melampaui batas maksimum (ceil(jumlah_peserta * 0.1)).

#### 8.3 Surat Undangan
- [ ] Upload surat undangan PDF pada halaman Bimtek (fitur generate file tersedia).
- [ ] Verify: Sistem hanya menyimpan file; pengiriman email dilakukan manual oleh panitia.

---

## Checklist Hasil Testing

### ✅ Fitur yang Harus Berfungsi:
- [ ] Login peserta & PIC berhasil
- [ ] Upload dokumen (Surat Tugas & SPPD)
- [ ] Validation upload (size, type)
- [ ] Middleware block peserta unverified
- [ ] PIC bisa approve dokumen
- [ ] Email sent saat semua dokumen approved
- [ ] Middleware allow peserta verified
- [ ] PIC bisa reject dokumen
- [ ] Email sent saat dokumen rejected (dengan catatan)
- [ ] Peserta bisa reupload dokumen rejected
- [ ] File lama terhapus saat reupload
- [ ] PIC/Panitia bypass middleware
- [ ] Bimtek tanpa verifikasi bypass middleware

### 📧 Email Notifications:
- [ ] Email "Dokumen Disetujui" (semua approved)
- [ ] Email "Dokumen Ditolak" (dengan catatan)

### 🔒 Authorization:
- [ ] Peserta tidak bisa akses dashboard verifikasi
- [ ] Peserta hanya bisa download dokumen sendiri
- [ ] PIC/Panitia bisa download semua dokumen

---

## Notes
- Server: http://127.0.0.1:8000
- Log errors: storage/logs/laravel.log
- Email log: Check mailbox atau log file (jika menggunakan log driver)

## Hasil Testing
[Isi hasil manual testing di sini]
