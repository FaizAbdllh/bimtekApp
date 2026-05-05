# Panduan Template Sertifikat (Final)

## 📌 Status Sistem Saat Ini

Sistem sertifikat sudah menggunakan **template standar tunggal BBPMP** dengan aturan:

- Tidak ada lagi upload template per-admin
- Tidak ada lagi menu manajemen template
- Output sertifikat **PDF-only**
- Ukuran cetak **A4 Landscape**

Dokumen ini menjadi panduan referensi konten template final yang dipakai aplikasi.

---

## 🧩 Placeholder yang Digunakan Sistem

Berikut daftar placeholder yang didukung oleh renderer sertifikat:

| Placeholder | Akan Diganti Dengan |
|-------------|---------------------|
| `${NAMA_PESERTA}` | Nama peserta (UPPERCASE) |
| `${NIP}` | NIP peserta |
| `${INSTANSI}` | Instansi/Dinas peserta |
| `${NOMOR_SERTIFIKAT}` | Nomor sertifikat (auto-generated) |
| `${JUDUL_BIMTEK}` | Judul kegiatan bimtek |
| `${TANGGAL_MULAI}` | Tanggal mulai (format Indonesia) |
| `${TANGGAL_SELESAI}` | Tanggal selesai (format Indonesia) |
| `${LOKASI}` | Lokasi penyelenggaraan |
| `${TANGGAL_TERBIT}` | Tanggal terbit sertifikat (format Indonesia) |

> Catatan: format tanggal menggunakan Bahasa Indonesia (contoh: `09 Maret 2026`).

---

## 🖼️ Struktur Layout Final

Layout final sertifikat yang aktif di sistem:

1. Logo BBPMP di bagian atas
2. Header institusi:
   - Balai Besar Penjaminan Mutu Pendidikan
   - BBPMP Sumatera Barat
   - Tahun
3. Judul besar: **SERTIFIKAT**
4. Nomor sertifikat di bawah judul
5. Blok penerima:
   - Nama peserta (center)
   - `NIP: ...` dan `Instansi: ...` dalam 2 baris center
6. Paragraf kegiatan bimtek
7. Tanggal terbit (kota, tanggal)
8. Blok tanda tangan Kepala BBPMP

---

## ✅ Checklist Verifikasi Cepat

Setiap selesai perubahan layout sertifikat, lakukan cek berikut:

1. Generate 1 sertifikat untuk peserta uji
2. Download file hasil
3. Pastikan:
   - Format file `.pdf`
   - 1 halaman
   - A4 landscape
   - Semua placeholder terisi benar
   - Tanggal tampil dalam Bahasa Indonesia

---

## 📞 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Layout berubah/hancur | Cek perubahan CSS di renderer `SertifikatTemplateService::renderAsHtml()` |
| Data placeholder tidak sesuai | Verifikasi mapping data peserta, bimtek, dan tanggal terbit |
| Tanggal tidak berbahasa Indonesia | Pastikan Carbon menggunakan locale `id` |
| File bukan PDF | Pastikan generator di `SertifikatController` tetap PDF-only |

---

## 📚 Referensi Teknis

- Renderer sertifikat: `app/Services/SertifikatTemplateService.php`
- Generator PDF: `app/Http/Controllers/SertifikatController.php`
- Aset logo: `public/images/logo-bbpmp.png`

---

**Updated:** 09 Maret 2026  
**Version:** 2.0  
**Status:** ✅ Sinkron dengan implementasi final
