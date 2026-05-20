# Test Flow: Surat Undangan Draft & Final

## Database Setup ✓
- [x] Roles: 7 roles tersedia (Persuratan ditambahkan)
- [x] Users: 16 users termasuk test user Persuratan
- [x] Bimteks: 6 bimteks tersedia untuk testing
- [x] Schema: Kolom draft dan final sudah di-refactor

## Test Users Ready
- **PIC (Pegawai Internal)**: Salmawilis (salmawillis@gmail.com)
- **Persuratan**: Bagian Persuratan (persuratan@bbpmp.go.id / password)
- **Test Bimtek**: "Advokasi Peningkatan Progres Belajar..." (Status: persiapan, PIC: Salmawilis)

---

## Test Scenario

### Phase 1: PIC Upload Draft Surat ✓
**Actor**: Pegawai Internal (Salmawilis)  
**Action**: Upload surat draft  
**Expected Result**:
- Form modal "upload-draft" muncul
- File PDF diterima
- Route `POST /bimtek/{bimtek}/upload-draft` dipanggil
- Database update: `file_surat_draft_path`, `file_surat_draft_uploaded_by`, `file_surat_draft_uploaded_at`
- Success message: "Draft surat undangan berhasil diupload"
- UI menampilkan draft file dengan uploader info: "Diunggah oleh: Salmawilis pada [timestamp]"

### Phase 2: PIC View Draft ✓
**Actor**: Pegawai Internal (Salmawilis)  
**Action**: Preview/Download draft  
**Expected Result**:
- Route `GET /bimtek/{bimtek}/preview-draft` menampilkan PDF inline
- Route `GET /bimtek/{bimtek}/download-draft` download PDF dengan nama "Surat Undangan Draft - [judul_final].pdf"

### Phase 3: Persuratan Download Draft (Offline Process)
**Actor**: Bagian Persuratan  
**Action**: Download draft dari sistem  
**Process**:
- (Offline) Persuratan membuka surat draft di Word/sistem mereka
- Tambah nomor surat, tanggal, tanda tangan Kepala
- Save sebagai final PDF

### Phase 4: Persuratan Upload Final Surat ✓
**Actor**: Bagian Persuratan (persuratan@bbpmp.go.id)  
**Action**: Upload surat final  
**Authorization**:
- Hanya role `Persuratan` yang bisa upload final
- Non-Persuratan akan dapat error 403 "Hanya Bagian Persuratan yang dapat mengunggah surat final"

**Expected Result**:
- Form modal "upload-final" muncul (hanya untuk Persuratan)
- File PDF diterima
- Route `POST /bimtek/{bimtek}/upload-final` dipanggil
- Database update: `file_surat_final_path`, `file_surat_final_uploaded_by`, `file_surat_final_uploaded_at`
- Success message: "Surat undangan final berhasil diupload oleh Bagian Persuratan"
- UI menampilkan final file dengan uploader info: "Diunggah oleh: Bagian Persuratan pada [timestamp]"

### Phase 5: PIC Download Final Surat ✓
**Actor**: Pegawai Internal (Salmawilis)  
**Action**: Download final surat untuk didistribusikan  
**Expected Result**:
- Route `GET /bimtek/{bimtek}/download-final` download PDF dengan nama "Surat Undangan Final - [judul_final].pdf"
- PIC dapat share ke WA grup / distribusi fisik ke instansi

---

## Authorization Matrix

| Aksi | PIC/Panitia | Persuratan | Admin IT | Kepala | PPK |
|------|-----------|-----------|---------|--------|-----|
| Upload Draft | ✓ (via `uploadDraft`) | ✗ | ✓ (implicit) | ✓ (implicit) | ✓ (implicit) |
| Preview Draft | ✓ | ✓ | ✓ | ✓ | ✓ |
| Download Draft | ✓ | ✓ | ✓ | ✓ | ✓ |
| Upload Final | ✗ | ✓ (via `uploadFinal` only) | ✓ (if isPersuratan) | ✗ | ✗ |
| Preview Final | ✓ | ✓ | ✓ | ✓ | ✓ |
| Download Final | ✓ | ✓ | ✓ | ✓ | ✓ |

---

## Code Changes Summary

### 1. Database (3 migrations)
- `2026_05_11_000001`: Add `file_surat_undangan_uploaded_by` (FK to users)
- `2026_05_11_000002`: Add `file_surat_undangan_uploaded_at` (timestamp)
- `2026_05_11_000003`: Refactor menjadi:
  - Draft: `file_surat_draft_path`, `file_surat_draft_uploaded_by`, `file_surat_draft_uploaded_at`
  - Final: `file_surat_final_path`, `file_surat_final_uploaded_by`, `file_surat_final_uploaded_at`

### 2. Model (Bimtek)
```php
// New relations
public function draftUploader(): BelongsTo
public function finalUploader(): BelongsTo

// New fillable fields (6)
'file_surat_draft_path', 'file_surat_draft_uploaded_by', 'file_surat_draft_uploaded_at',
'file_surat_final_path', 'file_surat_final_uploaded_by', 'file_surat_final_uploaded_at'
```

### 3. User Model
```php
public function isPersuratan(): bool // NEW
```

### 4. BimtekController
- `uploadDraft(Request, Bimtek)` - PIC/Panitia upload → draft folder
- `uploadFinal(Request, Bimtek)` - Persuratan only upload → final folder  
- `previewDraft(Bimtek)` - Preview draft PDF inline
- `downloadDraft(Bimtek)` - Download draft PDF
- `previewFinal(Bimtek)` - Preview final PDF inline
- `downloadFinal(Bimtek)` - Download final PDF

### 5. Routes
```php
POST   /{bimtek}/upload-draft   → uploadDraft   (name: 'upload-draft')
GET    /{bimtek}/preview-draft  → previewDraft  (name: 'preview-draft')
GET    /{bimtek}/download-draft → downloadDraft (name: 'download-draft')

POST   /{bimtek}/upload-final   → uploadFinal   (name: 'upload-final')
GET    /{bimtek}/preview-final  → previewFinal  (name: 'preview-final')
GET    /{bimtek}/download-final → downloadFinal (name: 'download-final')
```

### 6. View (bimtek/show.blade.php)
- Split surat section menjadi 2 card: Draft (blue) dan Final (green)
- Modal split: `upload-draft` dan `upload-final`
- Button visibility based on role dan status

### 7. Seeder
- `AddPersuratanRoleSeeder` - Add 'Persuratan' role jika tidak ada

---

## Manual Testing Checklist

### Pre-Test
- [ ] Database migrations run: `php artisan migrate`
- [ ] Seeder run: `php artisan db:seed --class=AddPersuratanRoleSeeder`
- [ ] Test user created: Persuratan (persuratan@bbpmp.go.id)
- [ ] Test bimtek available (status: persiapan)

### Test Execution
- [ ] **Draft Upload (PIC)**
  - [ ] Login: Salmawilis (PIC)
  - [ ] Open bimtek show page
  - [ ] Click "Upload Draft" button
  - [ ] Select PDF file & submit
  - [ ] Verify success message
  - [ ] Verify draft file appears with uploader info
  - [ ] Logout

- [ ] **Draft Preview/Download (Any user)**
  - [ ] Login: Different user (or Persuratan)
  - [ ] Open same bimtek
  - [ ] Click preview icon (draft section) → PDF displays inline
  - [ ] Click download icon (draft section) → PDF downloads with correct filename
  - [ ] Logout

- [ ] **Final Upload (Persuratan only)**
  - [ ] Login: Persuratan (persuratan@bbpmp.go.id)
  - [ ] Open same bimtek
  - [ ] Verify "Upload Final" button appears (green section)
  - [ ] Upload final PDF
  - [ ] Verify success message
  - [ ] Verify final file appears with Persuratan uploader info
  - [ ] Logout

- [ ] **Non-Persuratan Cannot Upload Final**
  - [ ] Login: Salmawilis (PIC)
  - [ ] Try to access `POST /bimtek/{id}/upload-final` directly
  - [ ] Verify 403 error or button hidden

- [ ] **Final Preview/Download (Any user)**
  - [ ] Login: Any user
  - [ ] Click preview icon (final section) → PDF displays inline
  - [ ] Click download icon (final section) → PDF downloads

### Validation
- [ ] No console errors in browser DevTools
- [ ] Database records correctly:
  - `file_surat_draft_path` populated
  - `file_surat_draft_uploaded_by` = PIC user ID
  - `file_surat_draft_uploaded_at` = upload timestamp
  - `file_surat_final_path` populated
  - `file_surat_final_uploaded_by` = Persuratan user ID
  - `file_surat_final_uploaded_at` = upload timestamp
- [ ] Files stored in correct folders:
  - Draft: `storage/app/public/surat-draft/`
  - Final: `storage/app/public/surat-final/`

---

## Expected Behavior

### Happy Path
1. PIC uploads draft → Draft file visible + metadata
2. Persuratan downloads draft offline, adds number/signature
3. Persuratan uploads final → Final file visible + metadata  
4. PIC downloads final → Ready to distribute

### Edge Cases
- Bimtek status != 'persiapan' → Upload button disabled (read-only)
- Non-Persuratan tries upload final → 403 Forbidden
- Missing file on download → Error message "File tidak ditemukan"
- Replace draft/final → Old file deleted, new file stored

---

## Success Criteria ✓
- [x] Role Persuratan created
- [x] Test user Persuratan created
- [x] Database schema refactored
- [x] Controllers implement draft & final methods
- [x] Routes added
- [x] Views show draft & final separately
- [x] Authorization enforced

**Ready for manual testing in browser!**
