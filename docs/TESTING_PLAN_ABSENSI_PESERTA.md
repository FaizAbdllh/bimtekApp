# Testing Plan: Absensi Peserta Bimtek

**Tanggal**: 11 Februari 2026  
**Status**: Ready to Test  
**Scope**: Fitur absensi peserta dengan QR Code + Time Lock  
**Version**: v2.0 - QR Code Implementation

---

## 📋 Executive Summary

Testing plan ini mencakup:
- ✅ Basic attendance functionality (13 test cases)
- ✅ **QR Code scanning system** (8 test cases) - **NEW**
- ✅ Security validation (5 layers)
- ✅ Edge cases & performance tests

**Key Features Tested:**
1. Manual attendance (legacy support)
2. **QR Code + Time Lock** (10-minute validity)
3. Session management (open/close)
4. Access control (verified peserta only)
5. Real-time attendance tracking

---

## 1. Prerequisites & Setup Data

### 1.1 Data yang Harus Ada
```
✓ Bimtek: "Workshop Pengimbasan oleh PSP Angkatan 1"
  - Status: berlangsung (agar peserta bisa akses absensi)
  - PIC: Salmawilis
  - Panitia: Chitra Puspitahati
  - Butuh Verifikasi: TRUE (dengan dokumen Surat Tugas, SPPD, KTP)

✓ Peserta Terverifikasi:
  - Faiz Abdullah: status_verifikasi = 'verified'
  - zafal: status_verifikasi = 'verified'

✓ Sesi Absensi:
  - Belum ada (akan dibuat via UI saat testing)
```

### 1.2 Users untuk Testing
| Role | Email | Password | Fungsi Testing |
|------|-------|----------|----------------|
| Panitia (Chitra) | chitra@bbpmp.test | password | Buat & kelola sesi absensi |
| Peserta (Faiz Abdullah) | faiz@bbpmp.test | password | Hadir di absensi |
| Peserta (zafal) | zafal@bbpmp.test | password | Hadir di absensi |

---

## 2. Test Scenarios

### Test Case 2.1: Akses Halaman Absensi (Peserta Terverifikasi)

**Precondition:**
- Login sebagai peserta Faiz Abdullah
- Status verifikasi = 'verified'
- Bimtek status = 'berlangsung'

**Steps:**
1. Klik menu **"Daftar Bimtek"** di sidebar
2. Klik detail bimtek "Workshop Pengimbasan oleh PSP Angkatan 1"
3. Klik tab **"Absensi"**

**Expected Result:**
- ✅ Halaman absensi dapat diakses
- ✅ Menampilkan daftar sesi absensi (jika ada)
- ✅ Tombol "Hadir" tersedia untuk sesi yang aktif

---

### Test Case 2.2: Akses Halaman Absensi (Peserta Belum Terverifikasi)

**Precondition:**
- Login sebagai peserta dengan status_verifikasi = 'invited' atau 'pending'

**Steps:**
1. Akses halaman bimtek
2. Cek tab "Absensi"

**Expected Result:**
- ✅ Tab "Absensi" DISABLED (greyed out)
- ✅ Muncul pesan: "Dokumen harus diverifikasi terlebih dahulu"
- ✅ Tidak bisa klik tab

---

### Test Case 2.3: Panitia Membuat Sesi Absensi

**Precondition:**
- Login sebagai Panitia (Chitra Puspitahati)
- Bimtek status = 'berlangsung'

**Steps:**
1. Buka detail bimtek
2. Klik tab **"Absensi"**
3. Klik tombol **"+ Buat Sesi Absensi"** (atau similar)
4. Isi form:
   - **Judul Sesi**: "Sesi 1 - Pembukaan"
   - **Tanggal**: 11 Feb 2026
   - **Jam Mulai**: 09:00
   - **Jam Selesai**: 11:00
5. Klik **"Simpan"**

**Expected Result:**
- ✅ Sesi absensi berhasil dibuat
- ✅ Muncul success message: "Sesi absensi berhasil dibuat"
- ✅ Sesi muncul di daftar dengan status 'aktif' atau 'berlangsung'
- ✅ Database terupdate di table `sesi_absensis`

---

### Test Case 2.4: Peserta Klik "Hadir" di Sesi Aktif

**Precondition:**
- Login sebagai peserta Faiz Abdullah (terverifikasi)
- Minimal 1 sesi absensi telah dibuat dengan status aktif

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Lihat sesi absensi yang tersedia
3. Klik tombol **"Hadir"** pada sesi tertentu
4. Konfirmasi (jika ada modal)

**Expected Result:**
- ✅ Peserta berhasil tercatat hadir
- ✅ Muncul success message
- ✅ Tombol berubah jadi "Sudah Hadir" atau disabled
- ✅ Database terupdate: `absensi_pesertas` dengan:
  - user_id = Faiz Abdullah
  - sesi_absensi_id = session yang diklik
  - status = 'hadir'
  - waktu_hadir = now()

---

### Test Case 2.5: Multiple Peserta Hadir di Sesi Sama

**Precondition:**
- Login sebagai Faiz Abdullah, hadir sesai Test 2.4
- 1 sesi absensi masih aktif

**Steps:**
1. Logout dari akun Faiz Abdullah
2. Login sebagai peserta zafal
3. Buka detail bimtek → tab "Absensi"
4. Klik **"Hadir"** pada sesi yang sama

**Expected Result:**
- ✅ zafal berhasil tercatat hadir
- ✅ Tidak konflik dengan absensi Faiz Abdullah
- ✅ Database punya 2 record absensi untuk sesi yang sama

---

### Test Case 2.6: Rekap Data Absensi (Panitia/PIC)

**Precondition:**
- Login sebagai Panitia atau PIC
- Minimal 2 peserta sudah hadir di sesi yang sama

**Steps:**
1. Buka detail bimtek
2. Klik tab **"Absensi"**
3. Klik **"Lihat Rekap"** atau view rekap absensi
4. Pilih sesi tertentu (jika ada dropdown)

**Expected Result:**
- ✅ Menampilkan tabel daftar peserta dengan kolom:
  - No, Nama Peserta, Status Kehadiran, Waktu Hadir
- ✅ Peserta yang hadir ditampilkan dengan ✓ atau "Hadir"
- ✅ Peserta yang tidak hadir ditampilkan dengan "-" atau "Tidak Hadir"
- ✅ Data akurat sesuai hasil di test case sebelumnya

---

### Test Case 2.7: Edit Sesi Absensi (Panitia)

**Precondition:**
- Login sebagai Panitia
- Minimal 1 sesi absensi telah dibuat
- Sesi belum memiliki banyak kehadiran (opsional)

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Cari sesi yang ingin diedit
3. Klik tombol **"Edit"** atau **"..."** (menu)
4. Ubah **"Judul Sesi"** misalnya jadi "Sesi 1 - Pembukaan (Diperbaharui)"
5. Klik **"Simpan"**

**Expected Result:**
- ✅ Sesi berhasil diupdate
- ✅ Muncul success message
- ✅ Judul sesi berubah di daftar
- ✅ Data absensi peserta tetap intact

---

### Test Case 2.8: Hapus Sesi Absensi (Panitia)

**Precondition:**
- Login sebagai Panitia
- Minimal 1 sesi absensi yang tidak memiliki kehadiran

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Klik tombol **"Hapus"** pada sesi
3. Konfirmasi di modal: "Yakin ingin menghapus sesi ini?"
4. Klik **"Hapus"**

**Expected Result:**
- ✅ Sesi absensi berhasil dihapus
- ✅ Muncul success message
- ✅ Sesi hilang dari daftar
- ✅ Data di database terhapus dari `sesi_absensis`

---

### Test Case 2.9: Tutup Sesi Absensi (Status)

**Precondition:**
- Login sebagai Panitia/PIC
- Sesi absensi status = 'aktif'

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Cari sesi yang ingin ditutup
3. Klik tombol **"Tutup"** atau ubah status jadi "Selesai"
4. Konfirmasi

**Expected Result:**
- ✅ Sesi status berubah menjadi 'selesai' atau 'closed'
- ✅ Peserta tidak bisa lagi klik "Hadir" untuk sesi ini
- ✅ Muncul pesan jika peserta coba hadir: "Sesi sudah ditutup"

---

### Test Case 2.10: Peserta Tidak Bisa Hadir Jika Belum Verifikasi

**Precondition:**
- Buat peserta baru dengan status_verifikasi = 'invited'
- Sesi absensi aktif

**Steps:**
1. Login sebagai peserta belum terverifikasi
2. Akses halaman bimtek
3. Cek tab "Absensi"
4. Coba akses (jika somehow bisa)

**Expected Result:**
- ✅ Tab "Absensi" disabled/tidak bisa di-klik
- ✅ Muncul tooltip: "Dokumen harus diverifikasi terlebih dahulu"
- ✅ Middleware `verified.peserta` block akses

---

### Test Case 2.11: Validasi Waktu Absensi

**Precondition:**
- Login sebagai peserta terverifikasi
- Sesi absensi ada dengan waktu spesifik (misal: 09:00-11:00)

**Steps:**
1. Buka detail bimtek
2. Klik "Hadir" pada sesi
3. Catat waktu hadir (system time)

**Expected Result:**
- ✅ Waktu hadir terekam otomatis dari `now()` / server time
- ✅ Format waktu: "HH:MM:SS" atau "DD-MM-YYYY HH:MM"
- ✅ Waktu hadir tidak bisa dimanipulasi client-side

---

### Test Case 2.12: Tambah Kehadiran Manual (Panitia)

**Precondition:**
- Login sebagai Panitia/PIC
- Sesi absensi ada
- Peserta belum hadir

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Klik detail sesi / rekap absensi
3. Klik tombol **"+ Tambah Kehadiran"**
4. Pilih peserta dari dropdown
5. Klik **"Simpan"**

**Expected Result:**
- ✅ Peserta ditambahkan ke daftar hadir manual
- ✅ Muncul success message
- ✅ Status peserta berubah jadi "Hadir" (dengan catatan: "Diinput manual")
- ✅ Waktu hadir = waktu input manual

---

### Test Case 2.13: Hapus Kehadiran (Panitia)

**Precondition:**
- Login sebagai Panitia/PIC
- Minimal ada 1 peserta yang hadir

**Steps:**
1. Buka detail sesi absensi
2. Lihat daftar peserta yang hadir
3. Klik tombol **"Hapus"** atau **"X"** pada peserta tertentu
4. Konfirmasi: "Hapus kehadiran peserta ini?"

**Expected Result:**
- ✅ Kehadiran peserta dihapus
- ✅ Peserta hilang dari daftar hadir
- ✅ Muncul success message
- ✅ Peserta bisa hadir lagi jika sesi masih aktif

---

### Test Case 2.14: Panitia Buka Sesi & Generate QR Code

**Precondition:**
- Login sebagai Panitia (Chitra Puspitahati)
- Minimal 1 sesi absensi telah dibuat dengan status 'ditutup'

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Klik tombol **"Buka Sesi"** pada sesi tertentu
3. Klik tombol **"Lihat QR Code"**

**Expected Result:**
- ✅ Sesi status berubah menjadi 'terbuka'
- ✅ QR Code otomatis ter-generate
- ✅ Halaman QR Code menampilkan:
  - QR Code berukuran besar (300x300px)
  - Countdown timer (valid 10 menit)
  - Timestamp berlaku hingga
  - Instruksi untuk peserta
- ✅ Database `sesi_absensis` terupdate:
  - qr_code = [unique_encoded_string]
  - qr_generated_at = now()
  - qr_expires_at = now() + 10 minutes

---

### Test Case 2.15: Peserta Scan QR Code (Valid)

**Precondition:**
- Login sebagai peserta Faiz Abdullah (terverifikasi)
- Panitia telah buka sesi & generate QR (Test 2.14)
- QR belum expired (<10 menit)

**Steps:**
1. Buka detail bimtek → tab "Absensi"
2. Klik tombol **"Scan QR Code"** pada sesi aktif
3. Copy QR data dari tampilan Panitia
4. Paste ke form input QR
5. Klik **"Verifikasi & Catat Kehadiran"**

**Expected Result:**
- ✅ Sistem validasi QR berhasil
- ✅ Kehadiran tercatat dengan timestamp
- ✅ Muncul success message: "Kehadiran Anda berhasil dicatat melalui QR Code"
- ✅ Redirect ke halaman detail sesi
- ✅ Status peserta berubah: "Sudah Hadir"
- ✅ Database `absensi_pesertas` terupdate

---

### Test Case 2.16: QR Code Expired (>10 Menit)

**Precondition:**
- Panitia generate QR
- Tunggu 11 menit (atau ubah manual `qr_expires_at` di database)

**Steps:**
1. Login sebagai peserta
2. Coba scan QR yang sudah expired
3. Submit form

**Expected Result:**
- ❌ Validasi gagal
- ❌ Error message: "QR Code tidak valid atau sudah kadaluarsa. Silakan minta QR Code baru dari panitia."
- ❌ Kehadiran TIDAK tercatat
- ✅ Peserta diminta scan QR baru

---

### Test Case 2.17: QR Code Salah/Invalid

**Precondition:**
- Login sebagai peserta
- Sesi aktif dengan QR valid

**Steps:**
1. Akses halaman scan QR
2. Input QR code yang salah/random string
3. Klik submit

**Expected Result:**
- ❌ Validasi gagal
- ❌ Error message: "QR Code tidak valid atau sudah kadaluarsa"
- ❌ Kehadiran TIDAK tercatat

---

### Test Case 2.18: Peserta Sudah Hadir Coba Scan Lagi

**Precondition:**
- Peserta Faiz Abdullah sudah hadir via QR (Test 2.15)
- Sesi masih aktif dengan QR valid

**Steps:**
1. Login sebagai Faiz Abdullah (sama)
2. Coba scan QR lagi untuk sesi yang sama

**Expected Result:**
- ❌ Error/info message: "Anda sudah tercatat hadir pada sesi ini"
- ❌ Kehadiran TIDAK double/duplicate
- ✅ One-time scan validation works

---

### Test Case 2.19: Sesi Ditutup, QR Tidak Valid

**Precondition:**
- Sesi status = 'terbuka' dengan QR valid
- Panitia menutup sesi

**Steps:**
1. Panitia klik **"Tutup Sesi"**
2. Peserta coba scan QR yang sama

**Expected Result:**
- ❌ Validasi gagal
- ❌ Error message: "QR Code tidak valid" atau "Sesi sudah ditutup"
- ❌ Kehadiran TIDAK tercatat

---

### Test Case 2.20: Generate QR Baru (Refresh QR)

**Precondition:**
- Sesi aktif dengan QR expired
- Login sebagai Panitia

**Steps:**
1. Buka halaman "Lihat QR Code"
2. Lihat status: "QR Code Kadaluarsa"
3. Klik tombol **"Generate QR Baru"**

**Expected Result:**
- ✅ QR baru ter-generate dengan data unik berbeda
- ✅ Timer reset (valid 10 menit lagi)
- ✅ Database update:
  - qr_code = [new_unique_string]
  - qr_generated_at = now()
  - qr_expires_at = now() + 10 minutes

---

### Test Case 2.21: Fallback Manual Attendance (Jika QR Tidak Tersedia)

**Precondition:**
- Login sebagai peserta
- Sesi aktif, tapi QR tidak available/expired

**Steps:**
1. Akses halaman scan QR
2. Klik dropdown **"Atau gunakan metode manual"**
3. Klik tombol **"Hadir Manual"**

**Expected Result:**
- ✅ Kehadiran tercatat tanpa QR
- ✅ Muncul success message
- ✅ Backward compatibility terjaga
- ✅ Admin bisa distinguish: absen via QR vs manual (optional)

---

## 3. Data Validation Tests

### Test 3.1: Database Integrity

**Cek:**
```sql
-- Sesi absensi ada
SELECT * FROM sesi_absensis WHERE bimtek_id = '[bimtek-id]';

-- Kehadiran peserta terekam
SELECT * FROM absensi_pesertas WHERE sesi_absensi_id = '[sesi-id]';

-- Foreign keys intact
SELECT * FROM absensi_pesertas 
WHERE user_id NOT IN (SELECT id FROM users)
   OR sesi_absensi_id NOT IN (SELECT id FROM sesi_absensis);
```

**Expected**: Tidak ada orphaned records

---

### Test 3.2: Middleware Check

**Test:** Peserta yang belum verify dokumen coba akses absensi
```bash
GET /bimtek/{bimtek}/absensi
```

**Expected:**
- ❌ 403 Forbidden (dari middleware `verified.peserta`)
- ❌ Atau redirect ke halaman daftar bimtek dengan pesan error

---

### Test 3.3: Authorization Check

**Test:** Peserta dari bimtek lain coba akses absensi bimtek ini
```bash
GET /bimtek/{bimtek}/absensi
```

**Expected:**
- ❌ 403 Forbidden
- ❌ Atau error message: "Anda tidak terdaftar di bimtek ini"

---

### Test 3.4: QR Code Security Validation

**Test 3.4.1: QR Data Integrity**
```sql
-- Cek QR code unik per sesi
SELECT qr_code, COUNT(*) 
FROM sesi_absensis 
WHERE qr_code IS NOT NULL 
GROUP BY qr_code 
HAVING COUNT(*) > 1;
```
**Expected**: 0 rows (tidak ada QR duplicate)

**Test 3.4.2: QR Expiration Logic**
```sql
-- Cek QR yang expired
SELECT id, nama_sesi, qr_expires_at, 
       NOW() > qr_expires_at AS is_expired
FROM sesi_absensis
WHERE qr_code IS NOT NULL;
```
**Expected**: Logic `is_expired` sesuai dengan 10 menit time window

**Test 3.4.3: Time Lock Validation**
- Generate QR pada waktu T
- Coba scan pada T+5 menit → ✅ Valid
- Coba scan pada T+11 menit → ❌ Invalid (expired)

**Test 3.4.4: Session Binding**
- QR dari Sesi A tidak bisa dipakai untuk Sesi B
- Expected: Validasi gagal dengan message: "QR Code tidak valid"

---

## 4. UI/UX Tests

### Test 4.1: Responsive Design
- Testing di mobile (iPhone SE)
- Testing di tablet (iPad)
- Testing di desktop

**Expected**: Tabel, tombol, form semua responsif dan mudah digunakan

### Test 4.2: Loading State
- Saat klik "Hadir", loadingnya jelas?
- Tombol disabled saat submit?
- Toast/notification muncul dengan baik?

---

## 5. Edge Cases

### Test 5.1: Peserta Klik Hadir 2x
**Expected**: 
- ❌ Error atau duplicate prevention
- ✅ Atau: Tombol berubah disabled, tidak bisa submit ulang

### Test 5.2: Sesi dengan 0 Peserta
**Expected**: 
- ✅ Bisa dibuat
- ✅ Rekapnya menampilkan: "Belum ada kehadiran"

### Test 5.3: Edit Sesi Saat Peserta Sedang Hadir
**Expected**: 
- ✅ Bisa edit (tidak kunci editing)
- ✅ Data kehadiran tetap aman

### Test 5.4: Bimtek Status Berubah dari "Berlangsung" ke "Selesai"
**Expected**: 
- ✅ Peserta tidak bisa hadir lagi
- ✅ Muncul pesan: "Bimtek sudah selesai, absensi tidak bisa dilakukan"

### Test 5.5: QR Dishare via WhatsApp (Security Test)
**Scenario**: Peserta A dapat QR, share ke Peserta B via WA
**Expected**: 
- ⏱️ QR expired dalam 10 menit (time lock)
- ⏱️ Jika scan >10 menit → ❌ Invalid
- ✅ Protection: Time window terlalu pendek untuk dishare
- ✅ Peserta B harus scan dalam window yang sama (imply: di lokasi)

### Test 5.6: Peserta Scan QR Sebelum Bimtek Berlangsung
**Scenario**: Bimtek status = 'persiapan', peserta coba scan QR
**Expected**: 
- ❌ Error: "Absensi hanya dapat dilakukan saat bimtek sedang berlangsung"
- ❌ Kehadiran TIDAK tercatat

### Test 5.7: Multiple Sesi dengan QR Berbeda
**Scenario**: Bimtek punya 3 sesi, masing-masing QR unik
**Expected**: 
- ✅ Setiap sesi generate QR berbeda
- ✅ QR Sesi 1 ≠ QR Sesi 2 ≠ QR Sesi 3
- ✅ Peserta bisa hadir di semua sesi dengan scan masing-masing

---

## 6. Performance Tests

### Test 6.1: Load Absensi dengan 100+ Peserta
**Expected**: 
- ✅ Halaman load < 2 detik
- ✅ Tabel responsif saat scroll

### Test 6.2: Search/Filter Peserta
**Expected**: 
- ✅ Filter real-time beroperasi lancar
- ✅ Tidak freeze UI

---

## 7. Checklist Testing

| No | Test Case | Status | Notes |
|---|---|---|---|
| 1 | Akses absensi (terverifikasi) | ⏳ | |
| 2 | Akses absensi (belum terverifikasi) | ⏳ | |
| 3 | Panitia buat sesi absensi | ⏳ | |
| 4 | Peserta hadir | ⏳ | |
| 5 | Multiple peserta hadir | ⏳ | |
| 6 | Rekap absensi | ⏳ | |
| 7 | Edit sesi | ⏳ | |
| 8 | Hapus sesi | ⏳ | |
| 9 | Tutup sesi | ⏳ | |
| 10 | Tambah kehadiran manual | ⏳ | |
| 11 | Hapus kehadiran | ⏳ | |
| **QR Code Tests** | | | |
| 12 | Panitia buka sesi & generate QR | ⏳ | |
| 13 | Peserta scan QR (valid) | ⏳ | |
| 14 | QR expired (>10 menit) | ⏳ | |
| 15 | QR salah/invalid | ⏳ | |
| 16 | Peserta sudah hadir coba scan lagi | ⏳ | |
| 17 | Sesi ditutup, QR tidak valid | ⏳ | |
| 18 | Generate QR baru (refresh) | ⏳ | |
| 19 | Fallback manual attendance | ⏳ | |
| 20 | QR data integrity (database) | ⏳ | |
| 21 | QR time lock validation | ⏳ | |
| 22 | QR session binding | ⏳ | |
| **Validation & Security** | | | |
| 23 | Validasi middleware | ⏳ | |
| 24 | Database integrity | ⏳ | |
| 25 | Authorization check | ⏳ | |
| 26 | QR security (share via WA) | ⏳ | |
| **UI/UX** | | | |
| 27 | UI responsif (mobile/tablet/desktop) | ⏳ | |
| 28 | Loading state & notifications | ⏳ | |
| **Edge Cases** | | | |
| 29 | Edge case: double hadir | ⏳ | |
| 30 | Edge case: sesi 0 peserta | ⏳ | |
| 31 | Edge case: edit sesi saat hadir | ⏳ | |
| 32 | Edge case: QR multiple sesi | ⏳ | |
| **Performance** | | | |
| 33 | Performance test (100+ peserta) | ⏳ | |

---

## 8. Notes & Observations

### QR Code Implementation Details
```
- Package: endroid/qr-code v6.0.9
- QR Format: base64_encode(session_id|timestamp|random_token)
- Validity: 10 minutes (configurable)
- Security Layers:
  1. Time Lock (10 min expiration)
  2. Session Binding (QR tied to specific session)
  3. One-time Scan (prevent duplicate)
  4. Status Validation (session must be open)
  5. Bimtek Status Check (must be "berlangsung")

- Views:
  * show-qr.blade.php - QR display for Panitia
  * scan-qr.blade.php - Scan interface for Peserta
  * show.blade.php - Updated with QR buttons

- Routes:
  * GET /bimtek/{id}/absensi/{sesi}/qr - Show QR
  * GET /bimtek/{id}/absensi/{sesi}/scan - Scan interface
  * POST /bimtek/{id}/absensi/{sesi}/scan - Validate & record
```

### Testing Notes
_Space untuk mencatat findings saat testing:_

```
[akan diisi saat testing]

Contoh format:
- [PASS] Test 2.14: QR generated successfully ✅
- [FAIL] Test 2.16: QR expired validation tidak bekerja ❌
  → Bug: qr_expires_at tidak ter-update
  → Fix: Update migration
```

---

## 10. Security Argument for Thesis Defense

### Pertanyaan Dosen: "Bagaimana mencegah kecurangan absensi?"

**Jawaban Terstruktur:**

Sistem menggunakan **QR Code + Time Lock** dengan 5 layer validasi:

#### Layer 1: Time Lock (10 Menit)
- QR Code hanya valid 10 menit sejak di-generate
- Automatic expiration mencegah sharing via WhatsApp
- Jika QR di-share >10 menit → otomatis invalid

#### Layer 2: Session Binding
- QR Code terikat ke session ID spesifik
- QR Sesi A tidak bisa dipakai untuk Sesi B
- Format: `base64(session_id|timestamp|random_token)`

#### Layer 3: One-Time Scan
- Peserta hanya bisa scan 1x per sesi
- Duplicate prevention di level database
- Constraint: `UNIQUE(user_id, sesi_absensi_id)`

#### Layer 4: Status Validation
- Sesi harus berstatus "terbuka"
- Bimtek harus berstatus "berlangsung"
- Double check di controller sebelum record

#### Layer 5: Audit Trail
- Setiap scan tercatat dengan timestamp
- Admin dapat detect suspicious patterns:
  - Multiple scan dalam 5 detik
  - Scan dari device berbeda
  - Scan outside time window

**Proof of Implementation:**
```php
// SesiAbsensi.php - Method isQrCodeValid()
public function isQrCodeValid(string $scannedQr): bool {
    if (!$this->qr_code) return false;
    if ($this->qr_code !== $scannedQr) return false;
    if (now()->isAfter($this->qr_expires_at)) return false;
    if (!$this->isOpen()) return false;
    return true;
}
```

**Trade-off Analysis:**
| Metode | Security | Usability | Complexity | Recommendation |
|--------|----------|-----------|------------|----------------|
| Button Only | ❌ Low | ✅ High | ✅ Low | ❌ Not recommended |
| QR + Time Lock | ✅ High | ✅ High | ⚠️ Medium | ✅ **Implemented** |
| Face Recognition | ✅ Very High | ❌ Low | ❌ High | ❌ Overkill |
| GPS Location | ⚠️ Medium | ❌ Low | ❌ High | ❌ Privacy concern |

**Conclusion:**  
QR Code + Time Lock provides **optimal balance** antara security, usability, dan implementation complexity untuk konteks akademik.

---

## 11. Deliverables

Setelah testing selesai:
- [ ] Semua test case passed (33 test cases total)
- [ ] QR Code scanning validated (8 QR-specific tests)
- [ ] Security validation completed (Time Lock + Session Binding)
- [ ] Screenshots/video evidence (jika ada bugs)
- [ ] Updated checklist dengan status final
- [ ] Bug report (jika ada)
- [ ] Performance metrics (jika relevan)
- [ ] Documentation of QR security measures for thesis defense

---

**Last Updated**: 11 Feb 2026  
**Tester**: [akan diisi]  
**Result**: [akan diisi]  
**Version**: v2.0 - QR Code Implementation**Total Test Cases**: 33 (13 basic + 8 QR specific + 12 validation/edge cases)