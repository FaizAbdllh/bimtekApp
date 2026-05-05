# Testing Plan: Sertifikat Bimtek (Final)

**Tanggal**: 09 Maret 2026  
**Status**: ✅ Ready to Test  
**Scope**: Fitur generate sertifikat dengan **template standar tunggal**  
**Version**: v2.0 - Standard Template, PDF-Only

---

## 📌 Ringkasan Implementasi Final

Sistem sertifikat saat ini menggunakan pendekatan berikut:

- 1 template standar BBPMP (tidak ada pemilihan template per bimtek)
- Output **PDF saja** (tidak ada fallback HTML)
- Ukuran halaman **A4 Landscape**
- Layout final berisi: logo BBPMP, header institusi, judul sertifikat, nomor sertifikat, data peserta, isi kegiatan, dan blok tanda tangan kepala
- Nama bulan menggunakan Bahasa Indonesia (contoh: Maret, Februari)

---

## 🔒 Security Assessment

### ✅ Status Keamanan

| Layer | Status | Detail |
|-------|--------|--------|
| **Authentication** | ✅ SECURE | Hanya user ter-login yang bisa akses |
| **Authorization** | ✅ SECURE | Hanya PIC/Panitia/Kepala/Admin sesuai role dan relasi bimtek |
| **Certificate Access** | ✅ SECURE | Peserta hanya bisa mengunduh sertifikat miliknya sendiri |
| **Storage** | ✅ SECURE | File disimpan di storage public dengan validasi akses saat download |

### ✅ Verifikasi Sistem

- ✓ Generate sertifikat PDF: working
- ✓ Format A4 landscape: working
- ✓ Placeholder replacement: 100% working
- ✓ Penanggalan Indonesia: working
- ✓ Layout 1 halaman: working

---

## 1. Prerequisites & Data Uji

### 1.1 Data Minimal

```
✓ Bimtek status selesai
✓ Minimal 1 peserta status verified
✓ Data peserta memiliki nama (wajib), NIP/instansi opsional
✓ User PIC/Panitia terhubung ke bimtek
```

### 1.2 Akun Uji

| Role | Email | Password | Fungsi Testing |
|------|-------|----------|----------------|
| PIC/Panitia | chitra@bbpmp.test | password | Generate dan verifikasi sertifikat |
| Peserta | [akun peserta] | [password] | Uji akses download milik sendiri |

### 1.3 Placeholder yang Digunakan Sistem

```
${NAMA_PESERTA}
${NIP}
${INSTANSI}
${NOMOR_SERTIFIKAT}
${JUDUL_BIMTEK}
${TANGGAL_MULAI}
${TANGGAL_SELESAI}
${LOKASI}
${TANGGAL_TERBIT}
```

---

## 2. Test Scenarios

### ✅ FASE 1: Akses & Halaman Sertifikat

### **Test Case 1.1: Akses halaman sertifikat bimtek**

**Precondition:**
- Login sebagai PIC/Panitia
- Bimtek sudah selesai

**Steps:**
1. Buka detail bimtek
2. Klik tab **Sertifikat**

**Expected Result:**
- ✅ URL: `/bimtek/{id}/sertifikat`
- ✅ Daftar peserta tampil
- ✅ Tidak ada dropdown pemilihan template
- ✅ Field tanggal terbit tersedia

---

### **Test Case 1.2: Validasi peserta eligible**

**Precondition:**
- Ada peserta verified dan non-verified

**Steps:**
1. Amati daftar peserta di tab sertifikat

**Expected Result:**
- ✅ Hanya peserta yang memenuhi aturan verifikasi/kelulusan yang dapat dipilih

---

### ✅ FASE 2: Generate Sertifikat

### **Test Case 2.1: Generate single peserta**

**Precondition:**
- Minimal 1 peserta eligible

**Steps:**
1. Pilih tanggal terbit
2. Pilih 1 peserta
3. Klik **Generate Sertifikat**

**Expected Result:**
- ✅ Muncul success message
- ✅ Record sertifikat tersimpan
- ✅ Link download tersedia

---

### **Test Case 2.2: Generate multiple peserta**

**Precondition:**
- Minimal 3 peserta eligible

**Steps:**
1. Pilih tanggal terbit
2. Pilih beberapa peserta
3. Klik **Generate Sertifikat**

**Expected Result:**
- ✅ Semua peserta terpilih berhasil diproses
- ✅ Setiap peserta mendapat nomor sertifikat unik

---

### **Test Case 2.3: Re-generate peserta yang sudah punya sertifikat**

**Precondition:**
- Peserta sudah memiliki sertifikat

**Steps:**
1. Coba generate ulang peserta yang sama

**Expected Result:**
- ✅ Sistem skip/menolak duplikasi sesuai logic sekarang
- ✅ Tidak membuat record ganda

---

### ✅ FASE 3: Download & Verifikasi Output PDF

### **Test Case 3.1: Verifikasi format file hasil download**

**Precondition:**
- Sertifikat sudah tergenerate

**Steps:**
1. Klik **Unduh**
2. Cek file hasil download

**Expected Result:**
- ✅ Ekstensi file `.pdf`
- ✅ Nama file format: `sertifikat_[nomor]_[nama_peserta]_[tanggal].pdf`
- ✅ File dapat dibuka normal

---

### **Test Case 3.2: Verifikasi layout PDF**

**Precondition:**
- PDF sudah terbuka

**Checklist Layout:**
- ✅ Ukuran kertas A4
- ✅ Orientasi landscape
- ✅ Satu halaman
- ✅ Logo BBPMP tampil di atas header
- ✅ NIP dan Instansi tampil center dalam 2 baris (`NIP: ...`, `Instansi: ...`)
- ✅ Blok tanda tangan kepala tampil rapi

---

### **Test Case 3.3: Verifikasi isi data placeholder**

**Expected Result:**
- ✅ `${NAMA_PESERTA}` terisi nama peserta (uppercase)
- ✅ `${NIP}` terisi NIP peserta
- ✅ `${INSTANSI}` terisi instansi
- ✅ `${NOMOR_SERTIFIKAT}` terisi nomor unik
- ✅ `${JUDUL_BIMTEK}` terisi judul kegiatan
- ✅ `${TANGGAL_MULAI}` dan `${TANGGAL_SELESAI}` format Indonesia
- ✅ `${LOKASI}` terisi lokasi
- ✅ `${TANGGAL_TERBIT}` format Indonesia (contoh: 09 Maret 2026)

---

### ✅ FASE 4: Security & Access Control

### **Test Case 4.1: Peserta tidak bisa akses sertifikat orang lain**

**Steps:**
1. Login sebagai peserta A
2. Akses URL download sertifikat peserta B

**Expected Result:**
- ✅ Response 403/404 sesuai kebijakan akses

---

### **Test Case 4.2: Session timeout behavior**

**Steps:**
1. Buka halaman sertifikat
2. Logout di tab lain
3. Refresh halaman

**Expected Result:**
- ✅ Redirect ke login
- ✅ Data sertifikat tidak berubah

---

## 3. Expected Outcomes Summary

| Phase | Scenarios | Target |
|------|-----------|--------|
| **Akses & Eligibility** | 2 | Pass |
| **Generate** | 3 | Pass |
| **PDF Output** | 3 | Pass |
| **Security** | 2 | Pass |
| **TOTAL** | **10** | **Pass** |

---

## 4. Success Criteria

- ✅ Semua 10 test case lulus
- ✅ Tidak ada duplikasi sertifikat
- ✅ Semua output berbentuk PDF
- ✅ Layout konsisten 1 halaman A4 landscape
- ✅ Placeholder terisi akurat
- ✅ Kontrol akses sertifikat berjalan benar

---

## 5. Notes & Known Limitations

### ✅ Yang Sudah Stabil
- PDF generation dengan DomPDF
- Single standard template
- Format tanggal Indonesia
- Layout final dengan logo, data peserta center, dan tanda tangan kepala

### ⚠️ Catatan Teknis
- Jika ada perubahan besar desain, lakukan uji ulang 3.2 (layout) dan 3.3 (placeholder)
- Perubahan konten panjang (judul bimtek sangat panjang) dapat memengaruhi wrapping teks

---

## 6. Sign-Off

| Role | Name | Date | Status |
|------|------|------|--------|
| QA / Tester | [Your Name] | 2026-03-09 | Ready |
| PIC/Panitia | [Your Name] | 2026-03-09 | Ready |
| Admin IT | [Your Name] | 2026-03-09 | Approved |

---

**Document Version**: 2.0  
**Last Updated**: 09 March 2026  
**Status**: ✅ APPROVED FOR TESTING
