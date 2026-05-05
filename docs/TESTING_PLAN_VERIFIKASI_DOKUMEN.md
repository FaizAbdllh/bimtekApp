# Testing Plan: Fitur Verifikasi Dokumen Peserta Bimtek

## Overview
Dokumen ini berisi rencana testing komprehensif untuk fitur verifikasi dokumen peserta yang baru diimplementasikan.

**Fitur:** Invited-Then-Verified Workflow untuk dokumen persyaratan peserta Bimtek (Surat Tugas & SPPD)

**Tanggal:** 4 Februari 2026

---

## 1. Unit Tests (Model & Business Logic)

### 1.1 Model DokumenPersyaratanPeserta
```php
// tests/Unit/DokumenPersyaratanPesertaTest.php

- test_it_belongs_to_bimtek()
- test_it_belongs_to_user_uploader()
- test_it_belongs_to_verifier()
- test_is_approved_method_returns_true_when_status_approved()
- test_is_rejected_method_returns_true_when_status_rejected()
- test_is_pending_method_returns_true_when_status_pending()
- test_get_file_url_returns_correct_url()
- test_casts_uploaded_at_as_datetime()
- test_casts_verified_at_as_datetime()
```

**Acceptance Criteria:**
- Semua relasi (bimtek, user, verifier) berfungsi dengan benar
- Helper methods (isApproved, isRejected, isPending) return nilai yang akurat
- Attribute casting untuk timestamp berfungsi
- File URL generation correct

### 1.2 Model Bimtek Relations
```php
// tests/Unit/BimtekTest.php (tambah test baru)

- test_has_many_dokumen_persyaratan()
- test_peserta_pending_verifikasi_relationship()
- test_peserta_verified_relationship()
- test_casts_jenis_dokumen_wajib_as_array()
- test_casts_butuh_verifikasi_dokumen_as_boolean()
```

**Acceptance Criteria:**
- Relasi `dokumenPersyaratan` return Collection
- `pesertaPendingVerifikasi` hanya return peserta dengan status_verifikasi = 'pending'
- `pesertaVerified` hanya return peserta dengan status_verifikasi = 'verified'
- JSON casting untuk `jenis_dokumen_wajib` berfungsi

---

## 2. Feature Tests (HTTP/Controllers)

### 2.1 PesertaController - Assignment dengan Notifikasi
```php
// tests/Feature/PesertaControllerTest.php

- test_assign_existing_peserta_sends_email_if_verification_required()
- test_assign_existing_peserta_sets_invited_status_if_verification_required()
- test_assign_existing_peserta_does_not_send_email_if_no_verification()
- test_store_new_peserta_sends_two_emails_if_verification_required()
- test_store_new_peserta_sets_invited_status_correctly()
```

**Test Scenarios:**

**Scenario A: Bimtek dengan Verifikasi**
```php
Given: Bimtek with butuh_verifikasi_dokumen = true
When: PIC assigns peserta
Then: 
  - Email PesertaBimtekInvitedMail terkirim
  - Pivot status_verifikasi = 'invited'
  - Pivot notified_at diisi dengan timestamp
  - Success message mention "Email undangan verifikasi dokumen telah dikirim"
```

**Scenario B: Bimtek tanpa Verifikasi**
```php
Given: Bimtek with butuh_verifikasi_dokumen = false
When: PIC assigns peserta
Then:
  - No verification email sent
  - Pivot status_verifikasi = NULL atau default
  - Pivot notified_at = NULL
```

### 2.2 VerifikasiDokumenController - Upload Flow
```php
// tests/Feature/VerifikasiDokumenControllerTest.php

- test_peserta_can_access_upload_form()
- test_non_peserta_cannot_access_upload_form()
- test_peserta_can_upload_surat_tugas()
- test_peserta_can_upload_sppd()
- test_upload_validates_file_type()
- test_upload_validates_file_size()
- test_upload_updates_status_to_pending()
- test_peserta_can_reupload_rejected_document()
- test_peserta_cannot_upload_without_being_invited()
```

**Test Scenarios:**

**Scenario C: Upload Dokumen Valid**
```php
Given: Peserta with status_verifikasi = 'invited'
When: Upload file PDF 1MB untuk surat_tugas
Then:
  - File tersimpan di storage/app/public/dokumen_persyaratan
  - Record baru di dokumen_persyaratan_peserta dengan status 'pending'
  - Pivot status_verifikasi berubah ke 'pending'
  - Redirect dengan success message
```

**Scenario D: Upload Invalid File**
```php
Given: Peserta with status_verifikasi = 'invited'
When: Upload file .exe atau file > 2MB
Then:
  - Validation error
  - File tidak tersimpan
  - Status tetap 'invited'
```

**Scenario E: Upload Ulang setelah Rejected**
```php
Given: Dokumen sebelumnya rejected
When: Upload ulang file baru
Then:
  - File lama terhapus dari storage
  - File baru tersimpan
  - Status dokumen kembali 'pending'
  - Peserta status_verifikasi = 'pending'
```

### 2.3 VerifikasiDokumenController - Verification Flow
```php
- test_pic_can_access_verification_dashboard()
- test_panitia_can_access_verification_dashboard()
- test_peserta_cannot_access_verification_dashboard()
- test_pic_can_approve_document()
- test_panitia_can_approve_document()
- test_approve_all_documents_sets_peserta_verified()
- test_approve_partial_documents_keeps_peserta_pending()
- test_pic_can_reject_document()
- test_reject_requires_catatan()
- test_reject_sends_email_notification()
- test_reject_changes_peserta_status_to_rejected()
```

**Test Scenarios:**

**Scenario F: Approve Single Document**
```php
Given: Peserta dengan 2 dokumen pending (surat_tugas + sppd)
When: Panitia approve surat_tugas
Then:
  - Dokumen surat_tugas status = 'approved'
  - Dokumen surat_tugas verified_by = panitia_id
  - Dokumen surat_tugas verified_at = now()
  - Peserta status_verifikasi masih 'pending' (karena sppd belum)
```

**Scenario G: Approve All Documents**
```php
Given: Peserta dengan 1 dokumen pending, 1 sudah approved
When: Panitia approve dokumen terakhir
Then:
  - Dokumen kedua status = 'approved'
  - Peserta status_verifikasi = 'verified'
  - Email DokumenVerifiedRejectedMail terkirim dengan status 'verified'
```

**Scenario H: Reject Document**
```php
Given: Peserta dengan dokumen pending
When: Panitia reject dengan catatan "Format tidak sesuai"
Then:
  - Dokumen status = 'rejected'
  - catatan_verifikasi = "Format tidak sesuai"
  - Peserta status_verifikasi = 'rejected'
  - Email notifikasi terkirim dengan catatan
```

### 2.4 VerifikasiDokumenController - Download
```php
- test_pic_can_download_any_document()
- test_panitia_can_download_any_document()
- test_peserta_can_download_own_document()
- test_peserta_cannot_download_others_document()
- test_download_returns_404_if_file_not_found()
```

---

## 3. Middleware Tests

### 3.1 CheckVerifiedPeserta Middleware
```php
// tests/Feature/CheckVerifiedPesertaMiddlewareTest.php
# Panduan Testing Sederhana: Verifikasi Dokumen Peserta

Dokumen ini adalah panduan singkat dan jelas untuk mengetes fitur verifikasi dokumen.
Fitur ini mengharuskan peserta mengunggah dokumen tertentu sebelum dapat mengakses Absensi, Tugas, dan Sertifikat.

---

## Ringkasan Aturan (Wajib Dipahami)
- Jenis dokumen wajib ditentukan oleh pengaju saat membuat pengajuan.
- Setelah Bimtek terbentuk, pengaturan dokumen tidak bisa diubah di Bimtek.
- Peserta hanya bisa mengakses Absensi/Tugas/Sertifikat jika status verifikasi = verified.

---

## A. Cek Pengaturan Dokumen di Pengajuan
1. Login sebagai Pegawai Internal.
2. Buat pengajuan baru.
3. Aktifkan verifikasi dokumen.
4. Tambahkan jenis dokumen wajib (contoh: "Surat Tugas", "KTP").
5. Submit pengajuan.

**Hasil yang diharapkan:**
- Pengajuan tersimpan.
- Jenis dokumen wajib yang dipilih tersimpan.

---

## B. Cek Pengaturan Dokumen di Bimtek
1. Approve pengajuan sampai Bimtek terbentuk.
2. Buka edit Bimtek.

**Hasil yang diharapkan:**
- Jenis dokumen wajib tampil.
- Field dokumen tidak bisa diubah (readonly).

---

## C. Cek Upload Dokumen oleh Peserta
1. Tambahkan peserta ke Bimtek.
2. Login sebagai peserta.
3. Buka halaman upload dokumen.
4. Upload semua dokumen yang diminta.

**Hasil yang diharapkan:**
- Upload berhasil.
- Status peserta berubah menjadi pending.

---

## D. Cek Verifikasi oleh PIC/Panitia
1. Login sebagai PIC/Panitia.
2. Buka dashboard verifikasi dokumen.
3. Setujui semua dokumen peserta.

**Hasil yang diharapkan:**
- Status dokumen = approved.
- Status peserta berubah menjadi verified.

---

## E. Cek Pembatasan Akses
1. Login sebagai peserta yang belum verified.
2. Coba akses Absensi/Tugas/Sertifikat.

**Hasil yang diharapkan:**
- Peserta diarahkan kembali ke halaman upload dokumen.

---

## F. Cek Validasi File
1. Upload file selain PDF/JPG/PNG.
2. Upload file lebih dari 2MB.

**Hasil yang diharapkan:**
- Sistem menolak file dan menampilkan error.

---

## Catatan
- Jika ada masalah, catat: langkahnya, hasil yang muncul, dan screenshot.
- Semua perubahan pengaturan dokumen harus dilakukan di tahap pengajuan.

### 11.3 Browser Compatibility
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Chrome (Android)
- [ ] Mobile Safari (iOS)

### 11.4 Responsive Design
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

---

## 12. Regression Tests

Pastikan fitur lama tidak break:

- [ ] Assign peserta ke Bimtek tanpa verifikasi masih works
- [ ] Absensi untuk Bimtek tanpa verifikasi masih accessible
- [ ] Tugas untuk Bimtek tanpa verifikasi masih bisa dikumpulkan
- [ ] Sertifikat generate untuk Bimtek tanpa verifikasi masih works
- [ ] PIC/Panitia dapat akses semua fitur tanpa blocked middleware

---

## 13. Database Seeding untuk Testing

### 13.1 Seed Data Required

```php
// database/seeders/VerificationTestSeeder.php

- 1 Bimtek dengan verifikasi ON (surat_tugas + sppd)
- 1 Bimtek dengan verifikasi OFF
- 3 Peserta:
  - Peserta A: status invited
  - Peserta B: status pending (1 doc uploaded)
  - Peserta C: status verified (all docs approved)
- 1 PIC (user_id sesuai)
- 2 Panitia
- 5 Dokumen sample:
  - 2 pending
  - 2 approved
  - 1 rejected
```

**Command:**
```bash
php artisan db:seed --class=VerificationTestSeeder
```

---

## 14. Test Execution Order

1. **Unit Tests** (paling cepat, run first)
2. **Feature Tests** (HTTP/Controllers)
3. **Middleware Tests**
4. **Integration Tests** (Email)
5. **Manual Testing** (E2E scenarios)
6. **Performance Tests** (optional, run separate)
7. **Security Tests** (before deployment)

**Command to Run:**
```bash
# All tests
php artisan test

# Specific test suite
php artisan test --testsuite=Feature --filter=Verification

# With coverage
php artisan test --coverage

# Parallel execution
php artisan test --parallel
```

---

## 15. Success Criteria

Fitur dianggap **PASS** jika:

✅ **Functional:**
- Semua automated tests green (100% pass rate)
- Manual E2E happy path berhasil tanpa error
- Email terkirim dengan konten yang benar

✅ **Non-Functional:**
- Dashboard load time < 1 detik (100 peserta)
- Upload file < 2 detik (2MB file)
- No memory leak pada long-running processes

✅ **Security:**
- No SQL injection vulnerabilities
- No XSS vulnerabilities
- Authorization checks passed

✅ **UX:**
- Responsive di semua device
- Error messages jelas dan helpful
- Success feedback immediate

---

## 16. Known Issues & Limitations

Document any issues found during testing:

1. **Issue:** [Describe]
   - **Severity:** High/Medium/Low
   - **Workaround:** [If any]
   - **Fix ETA:** [Date]

---

## 17. Test Environment Setup

### Required:
- Laravel 11.x
- PHP 8.2+
- MySQL 8.0+
- Storage writable permissions
- Mail driver configured (log/mailtrap/smtp)

### Setup Commands:
```bash
# Copy env
cp .env.example .env.testing

# Generate key
php artisan key:generate --env=testing

# Run migrations
php artisan migrate --env=testing

# Seed test data
php artisan db:seed --class=VerificationTestSeeder --env=testing

# Link storage
php artisan storage:link

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 18. Post-Testing Cleanup

After testing:
```bash
# Reset database
php artisan migrate:fresh

# Clear uploaded test files
rm -rf storage/app/public/dokumen_persyaratan/*

# Clear logs
> storage/logs/laravel.log
```

---

## Sign-off

**Tested By:** _______________________  
**Date:** _______________________  
**Status:** ☐ Pass ☐ Fail ☐ Partial  
**Notes:** _______________________

---

**Next Steps:**
1. Create test files in `tests/Feature/` and `tests/Unit/`
2. Implement test cases using PHPUnit
3. Run tests and document results
4. Fix any bugs found
5. Deploy to staging for UAT
