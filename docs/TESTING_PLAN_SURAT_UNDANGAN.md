# Testing Plan: Fitur Surat Undangan Bimtek

**Tanggal**: 26 Maret 2026  
**Status**: ✅ Ready to Test  
**Scope**: Upload, preview, download, dan pengiriman sebagai email attachment  
**Version**: v1.0 - Single Invitation File per Bimtek  

---

## 📌 Ringkasan Implementasi

Fitur surat undangan mencakup:

- **Upload** file surat undangan (`PDF`, maks `5MB`) per bimtek
- **Penyimpanan** path file di `bimteks.file_surat_undangan_path`
- **Preview** file PDF secara inline di browser
- **Download** file PDF ke lokal
- **Otomatis lampiran email** peserta (jika file tersedia) pada:
  - Email undangan verifikasi dokumen (`PesertaBimtekInvitedMail`)
  - Email notifikasi ditambahkan ke bimtek (`PesertaAddedToBimtekMail`)
  - Email kredensial peserta baru (`PesertaCredentialsMail` dengan konteks bimtek)

---

## 🔒 Security Assessment

| Layer | Status | Detail |
|-------|--------|--------|
| **Authentication** | ✅ SECURE | Hanya user login yang bisa akses endpoint terkait |
| **Authorization** | ✅ SECURE | Hanya role berwenang (PIC/Panitia/Admin sesuai policy) yang bisa upload/manage |
| **File Access** | ✅ SECURE | Preview/download validasi path & file existence |
| **Validation** | ✅ SECURE | MIME `pdf`, max `5120KB` pada upload |
| **Storage** | ✅ SECURE | File disimpan di `storage/app/public/surat-undangan/` |

---

## 1. Prerequisites & Data Uji

### 1.1 Data Minimal yang Diperlukan

```
✓ Minimal 1 data bimtek aktif (status persiapan/berlangsung/selesai)
✓ Akun PIC/Panitia yang punya akses kelola bimtek
✓ Minimal 2 akun peserta (1 existing user, 1 new user untuk testing berbeda skenario)
✓ SMTP/testing mailer aktif (localhost:1025 untuk Mailpit, sandbox provider, atau testing mode)
```

### 1.2 File Uji yang Diperlukan

| File | Ukuran | Halaman | Fungsi |
|------|--------|---------|--------|
| `undangan-valid.pdf` | ~100KB | 1 | Test upload normal |
| `undangan-multipage.pdf` | ~500KB | 2-3 | Test multi-page (seperti contoh real) |
| `undangan-over-5mb.pdf` | >5MB | - | Test validasi ukuran max |
| `undangan-invalid.docx` | - | - | Test validasi format |

### 1.3 Akun Uji

| Role | Email | Password | Fungsi Testing |
|------|-------|----------|----------------|
| PIC/Panitia | pic@bbpmp.test | password | Upload/manage undangan, tambah peserta |
| Peserta Existing | peserta1@test.id | password | Uji email notifikasi + attachment |
| Peserta Baru | - | auto-generated | Uji email kredensial + attachment |

---

## 2. Test Scenarios

### ✅ **FASE 1: Upload & Validasi Surat Undangan**

### Test Case 1.1 - Upload PDF Valid
**Precondition:**
- Login sebagai PIC/Panitia
- Akses halaman detail bimtek

**Steps:**
1. Cari section upload surat undangan
2. Pilih file `undangan-valid.pdf` (100KB, 1 halaman)
3. Klik tombol upload

**Expected Result:**
- ✅ Muncul success message "Surat undangan berhasil diupload."
- ✅ `file_surat_undangan_path` terisi di database
- ✅ File tersimpan di `storage/app/public/surat-undangan/{uuid}.pdf`
- ✅ Tombol preview/download muncul

---

### Test Case 1.2 - Replace File Undangan Lama
**Precondition:**
- File undangan lama sudah ada di bimtek

**Steps:**
1. Upload file baru `undangan-multipage.pdf` (3 halaman)
2. Submit

**Expected Result:**
- ✅ File lama dihapus dari storage secara otomatis
- ✅ Path DB mengarah ke file baru
- ✅ Preview/download menggunakan file baru
- ✅ Tidak ada duplikasi file di storage

---

### Test Case 1.3 - Validasi Format File (Hanya PDF)
**Steps:**
1. Upload file `undangan-invalid.docx`
2. Submit

**Expected Result:**
- ✅ Validation error muncul: "File harus bertipe PDF"
- ✅ File tidak tersimpan
- ✅ Path undangan di database tidak berubah
- ✅ User kembali ke form dengan error message

---

### Test Case 1.4 - Validasi Ukuran File (Max 5MB)
**Steps:**
1. Upload file `undangan-over-5mb.pdf` (ukuran >5MB)
2. Submit

**Expected Result:**
- ✅ Validation error: "File terlalu besar (maks 5MB)"
- ✅ File tidak tersimpan
- ✅ Path undangan tidak berubah

---

### ✅ **FASE 2: Preview & Download Surat Undangan**

### Test Case 2.1 - Preview Surat Undangan di Browser
**Precondition:**
- File undangan tersedia di bimtek

**Steps:**
1. Klik tombol "Preview Undangan"
2. Tunggu halaman load

**Expected Result:**
- ✅ Response type: `application/pdf` dengan `Content-Disposition: inline`
- ✅ PDF tampil di inline viewer browser (tidak download langsung)
- ✅ Semua halaman PDF terlihat (jika multi-page, user bisa scroll/navigate)
- ✅ Toolbar PDF tersedia (zoom, print, download, dll)

---

### Test Case 2.2 - Download Surat Undangan
**Precondition:**
- File undangan tersedia

**Steps:**
1. Klik tombol "Download Undangan"
2. Tunggu file selesai download

**Expected Result:**
- ✅ Response type: `application/octet-stream`
- ✅ Browser otomatis download file
- ✅ Nama file: `Surat Undangan - {judul_bimtek}.pdf`
- ✅ File dapat dibuka normal di PDF reader
- ✅ Isi file lengkap dan valid (jika multi-page, semua halaman ada)

---

### Test Case 2.3 - Access Preview saat File Tidak Ada / Terhapus
**Precondition:**
- Path DB terisi tapi file fisik hilang / tidak ada file

**Steps:**
1. Akses preview atau download
2. Amati response

**Expected Result:**
- ✅ Tidak crash / error 500
- ✅ Muncul error message: "File surat undangan tidak ditemukan."
- ✅ User redirect kembali ke halaman detail bimtek

---

### ✅ **FASE 3: Integrasi Email Attachment**

### Test Case 3.1 - Tambah Peserta Existing (Bimtek dengan Verifikasi Dokumen ON)
**Precondition:**
- Bimtek dengan `butuh_verifikasi_dokumen = true`
- File undangan sudah diupload
- User existing (email: peserta1@test.id) belum ada di bimtek

**Steps:**
1. Login sebagai PIC/Panitia
2. Buka halaman kelola peserta
3. Tambahkan user existing sebagai peserta
4. Submit

**Expected Result:**
- ✅ Success message: "Peserta berhasil ditambahkan. Email undangan verifikasi dokumen telah dikirim."
- ✅ Email `PesertaBimtekInvitedMail` terkirim ke peserta1@test.id
- ✅ Body email mencantumkan: "📎 Surat Undangan Terlampir"
- ✅ Attachment PDF ada dengan nama: `surat-undangan-{slug-judul}.pdf`
- ✅ Peserta dapat membuka attachment tanpa error

---

### Test Case 3.2 - Tambah Peserta Existing (Bimtek TANPA Verifikasi Dokumen)
**Precondition:**
- Bimtek dengan `butuh_verifikasi_dokumen = false`
- File undangan sudah diupload
- User existing baru (berbeda dari test 3.1)

**Steps:**
1. Tambahkan user sebagai peserta
2. Submit

**Expected Result:**
- ✅ Email `PesertaAddedToBimtekMail` terkirim
- ✅ Body email contain: "📎 Surat Undangan Terlampir"
- ✅ Attachment ikut terkirim

---

### Test Case 3.3 - Buat Peserta Baru (New Account)
**Precondition:**
- File undangan tersedia
- User baru belum ada di sistem (email: newuser@test.id)

**Steps:**
1. Login sebagai PIC/Panitia
2. Dalam halaman kelola peserta, pilih "Tambah Peserta Baru"
3. Isi form: Nama, Email (newuser@test.id), NIP, Instansi
4. Submit

**Expected Result:**
- ✅ Akun baru dibuat otomatis
- ✅ Email credential terkirim ke newuser@test.id
- ✅ Email credential berisi password temporary
- ✅ Jika bimtek dengan verifikasi: email undangan juga terkirim dengan attachment
- ✅ Jika bimtek tanpa verifikasi: attachment ikut terkirim di email credential
- ✅ Attachment dapat dibuka

---

### Test Case 3.4 - Import Peserta CSV
**Precondition:**
- File undangan tersedia
- CSV berisi mix: 2 existing users, 2 new users (yang belum terdaftar)

**Steps:**
1. Buka halaman import peserta
2. Upload file CSV
3. Submit

**Expected Result:**
- ✅ Existing users dalam CSV menerima email notifikasi + attachment
- ✅ New users dalam CSV menerima email credential + attachment
- ✅ Success message menampilkan: "X peserta ditambahkan, Y akun baru dibuat"
- ✅ Email notifikasi mezclude attachment undangan

---

### Test Case 3.5 - Email Peserta saat File Undangan BELUM Diupload
**Precondition:**
- Bimtek ada tapi `file_surat_undangan_path` kosong / null

**Steps:**
1. Tambahkan peserta baru

**Expected Result:**
- ✅ Email tetap terkirim normal (PesertaBimtekInvitedMail atau PesertaAddedToBimtekMail)
- ✅ TANPA attachment (graceful fallback)
- ✅ Body email tidak menampilkan "📎 Surat Undangan Terlampir"
- ✅ Tidak ada error/exception pada saat kirim email
- ✅ Proses assignment peserta tetap sukses

---

### ✅ **FASE 4: Authorization & Negative Test**

### Test Case 4.1 - Non-PIC/Panitia Coba Upload Undangan
**Precondition:**
- Login sebagai role yang tidak berwenang (misal: Peserta, Admin tanpa akses bimtek tertentu)

**Steps:**
1. Coba akses endpoint upload undangan langsung atau via UI

**Expected Result:**
- ✅ Response 403 / redirect unauthorized
- ✅ Muncul message: "Anda tidak memiliki akses untuk bimtek ini"
- ✅ File tidak berubah

---

### Test Case 4.2 - User dari Bimtek Lain Coba Download Undangan
**Precondition:**
- User login terkait bimtek A, coba akses undangan bimtek B

**Steps:**
1. Akses URL langsung: `/bimtek/{bimtek_b_id}/undangan/download`

**Expected Result:**
- ✅ 403 / redirect unauthorized
- ✅ File attachment bimtek B tidak diakses

---

### Test Case 4.3 - Stabilitas saat Pengiriman Email Gagal
**Simulasi:** Putuskan koneksi SMTP / set mailer ke invalid  
**Precondition:**
- File undangan tersedia
- SMTP tidak aktif/error

**Steps:**
1. Tambah peserta baru

**Expected Result:**
- ✅ Proses tambah peserta tetap **BERHASIL** (tidak rollback akibat email fail)
- ✅ Peserta tetap ter-assign ke bimtek
- ✅ Error email tercatat di log dengan level `error`
- ✅ Success message tetap muncul (dengan catatan email mungkin gagal)
- ✅ Sistem tidak crash

---

### Test Case 4.4 - Attachment File Tidak Ditemukan saat Email Dikirim
**Simulasi:** File path terisi tapi file fisik dihapus setelah upload, saat user di-add  
**Precondition:**
- File path tersimpan di DB
- File fisik dihapus dari disk

**Steps:**
1. Tambah peserta

**Expected Result:**
- ✅ Email tetap terkirim (tanpa attachment)
- ✅ Tidak ada error fatal
- ✅ Log mencatat file not found (level warning/info)

---

## 3. Test Coverage Summary

| Phase | Scenarios | Total |
|-------|-----------|-------|
| **Upload & Validasi** | 4 | 4 |
| **Preview & Download** | 3 | 3 |
| **Integrasi Email** | 5 | 5 |
| **Authorization & Negative** | 4 | 4 |
| **TOTAL** | | **16** |

---

## 4. Success Criteria (Acceptance)

✅ **Semua endpoint** (`upload`, `preview`, `download`) berjalan sesuai role & authorization  
✅ **Hanya 1 file aktif** per bimtek (replace dengan delete file lama)  
✅ **Validasi file** (format PDF, max 5MB) bekerja dengan baik  
✅ **Email peserta** mengandung attachment jika file tersedia  
✅ **Email tetap terkirim** tanpa attachment jika file tidak ada (graceful)  
✅ **Tidak ada error fatal** pada skenario negative / SMTP failure  
✅ **Multi-page PDF** tetap valid sebagai attachment (semua halaman ikut)  
✅ **Log aplikasi** mencatat warning/error sesuai expectation  

---

## 5. Evidence Checklist (Wajib Dikumpulkan QA)

### Upload & Validation
- [ ] Screenshot success upload
- [ ] Screenshot error validation (format/size)
- [ ] Bukti file lama terhapus saat replace

### Preview & Download
- [ ] Screenshot preview PDF di browser (multi-page terlihat)
- [ ] File hasil download (bisa dibuka)
- [ ] Screenshot error saat file tidak ada

### Email Integration
- [ ] Screenshot inbox peserta dengan attachment
- [ ] Bukti attachment bisa dibuka (screenshot preview)
- [ ] Email tanpa attachment (file belum diupload)
- [ ] Log aplikasi untuk SMTP failure scenario

### Authorization & Security
- [ ] Screenshot 403 saat user non-authorized
- [ ] Log error untuk access denied

---

## 6. Catatan Teknis

| Aspek | Detail |
|-------|--------|
| **Controller** | `BimtekController@uploadUndangan`, `previewUndangan`, `downloadUndangan` |
| **Mailable** | `PesertaBimtekInvitedMail`, `PesertaAddedToBimtekMail`, `PesertaCredentialsMail` |
| **Storage** | Disk `public`, folder `surat-undangan/` |
| **DB Field** | `bimteks.file_surat_undangan_path` |
| **Validation** | `mimes:pdf`, `max:5120` (5MB) |
| **Attachment** | `Attachment::fromStorageDisk('public', $path)` dengan MIME `application/pdf` |
| **Filename** | Auto-generated: `surat-undangan-{slug-judul}.pdf` |

---

## 7. Recommended Test Environment

```bash
# Setup test/local environment
- Database: fresh/seeded dengan minimal data bimtek
- Mail: Mailpit (localhost:1025) atau Mailtrap sandbox
- Storage: symlinked ke public/storage
- Config: APP_DEBUG=true, LOG_LEVEL=debug
```

**Perintah test mailer (Mailpit via Docker):**
```bash
docker run --rm --name mailpit -p 1025:1025 -p 8025:8025 axllent/mailpit
# Akses UI: http://localhost:8025
```

---

