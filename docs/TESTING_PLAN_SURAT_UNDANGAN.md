# Testing Plan: Surat Undangan Bimtek

**Tanggal**: 11 Mei 2026  
**Status**: Ready untuk manual test  
**Scope**: Draft surat oleh PIC/Panitia, final surat oleh Persuratan, preview/download, dan authorization  
**Version**: v2.0 - Draft/Final Split Workflow

---

## Ringkasan Workflow

Workflow surat undangan sekarang memakai dua tahap yang terpisah:

- **Draft surat** diunggah oleh **PIC atau Panitia**.
- **Final surat** diunggah oleh **Bagian Persuratan** setelah proses offline selesai.
- File draft dan final disimpan terpisah.
- Masing-masing file punya metadata uploader dan timestamp sendiri.

### Alur singkat
1. PIC/Panitia upload draft.
2. Persuratan download draft, proses offline, lalu upload final.
3. Semua role yang punya akses ke bimtek dapat preview/download sesuai otorisasi.

---

## Data Uji yang Dibutuhkan

- Minimal 1 bimtek dengan status `persiapan`.
- User PIC yang menjadi owner bimtek.
- User Panitia yang ter-attach ke bimtek.
- User Persuratan.
- File PDF valid untuk draft dan final.

### Akun uji lokal

Gunakan akun seed/test yang sudah tersedia di environment ini.
Jika perlu cek ulang credential lokal yang aktif di seeder atau data dummy yang sudah disiapkan.

---

## Endpoint yang Diuji

- `POST /bimtek/{bimtek}/upload-draft`
- `GET /bimtek/{bimtek}/preview-draft`
- `GET /bimtek/{bimtek}/download-draft`
- `POST /bimtek/{bimtek}/upload-final`
- `GET /bimtek/{bimtek}/preview-final`
- `GET /bimtek/{bimtek}/download-final`

---

## Checklist Manual Cepat

Gunakan checklist ini jika ingin test singkat sebelum commit:

- [ ] Login sebagai PIC, upload draft PDF, lalu pastikan file draft muncul.
- [ ] Login sebagai Panitia, upload draft PDF, lalu pastikan draft bisa diganti.
- [ ] Login sebagai Persuratan, upload final PDF, lalu pastikan file final muncul.
- [ ] Login sebagai PIC atau Panitia, coba upload final, dan pastikan ditolak `403`.
- [ ] Klik preview draft dan pastikan PDF tampil inline.
- [ ] Klik preview final dan pastikan PDF tampil inline.
- [ ] Klik download draft dan pastikan file terunduh.
- [ ] Klik download final dan pastikan file terunduh.

---

## Skenario Manual Test

### 1. Upload Draft oleh PIC

**Precondition**
- Login sebagai PIC.
- Bimtek berada pada status `persiapan`.

**Steps**
1. Buka halaman detail bimtek.
2. Klik tombol upload draft.
3. Pilih file PDF valid.
4. Submit.

**Expected**
- Draft tersimpan.
- Muncul success message.
- Field draft terisi di database.
- Metadata uploader dan timestamp terisi.

---

### 2. Upload Draft oleh Panitia

**Precondition**
- Login sebagai Panitia yang ter-attach ke bimtek.

**Steps**
1. Buka detail bimtek.
2. Upload file draft PDF.
3. Submit.

**Expected**
- Upload berhasil.
- Draft dapat diganti jika file baru diunggah.
- File lama terhapus dari storage.

---

### 3. Upload Final oleh Persuratan

**Precondition**
- Draft sudah ada.
- Login sebagai user Persuratan.

**Steps**
1. Buka detail bimtek.
2. Klik upload final.
3. Pilih PDF final.
4. Submit.

**Expected**
- Final tersimpan.
- Success message muncul.
- Field final terisi di database.
- Metadata uploader final dan timestamp terisi.

---

### 4. Non-Persuratan Coba Upload Final

**Precondition**
- Login sebagai PIC atau Panitia.

**Steps**
1. Akses endpoint upload final.
2. Submit file final.

**Expected**
- Response `403 Forbidden`.
- Final tidak berubah.
- Tidak ada file baru di storage.

---

### 5. Preview dan Download Draft

**Precondition**
- Draft tersedia.

**Steps**
1. Klik preview draft.
2. Klik download draft.

**Expected**
- Preview tampil inline sebagai PDF.
- Download mengunduh file PDF.
- Filename sesuai format surat undangan draft.

---

### 6. Preview dan Download Final

**Precondition**
- Final tersedia.

**Steps**
1. Klik preview final.
2. Klik download final.

**Expected**
- Preview tampil inline sebagai PDF.
- Download mengunduh file PDF final.
- Filename sesuai format surat undangan final.

---

### 7. File Tidak Ada di Storage

**Precondition**
- Path di database masih ada, tetapi file fisik di storage dihapus manual.

**Steps**
1. Coba preview atau download draft/final.

**Expected**
- Tidak error 500.
- Muncul pesan file tidak ditemukan.
- User kembali ke halaman detail bimtek.

---

## Checklist Validasi

- [ ] Draft upload oleh PIC berhasil.
- [ ] Draft upload oleh Panitia berhasil.
- [ ] Final upload oleh Persuratan berhasil.
- [ ] Non-Persuratan tidak bisa upload final.
- [ ] Preview draft berhasil.
- [ ] Preview final berhasil.
- [ ] Download draft berhasil.
- [ ] Download final berhasil.
- [ ] Metadata uploader draft tampil benar.
- [ ] Metadata uploader final tampil benar.
- [ ] File lama terhapus saat upload ulang.
- [ ] Response error untuk file hilang aman.

---

## Hasil yang Diharapkan

Jika semua skenario di atas berhasil, maka workflow surat undangan dianggap siap untuk commit.

---

## Catatan Teknis

- Draft disimpan terpisah dari final.
- Persuratan menjadi satu-satunya role yang boleh upload final.
- PIC/Panitia tetap bisa mengelola draft sesuai akses bimtek.
- Manual test ini fokus pada workflow terbaru, bukan lagi skema single-file lama.
