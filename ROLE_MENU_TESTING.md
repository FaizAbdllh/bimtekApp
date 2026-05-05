# Quick Reference - Menu per Role

## 🔐 Login Credentials
**Password untuk semua user: `password`**

| Role | Name | Email |
|------|------|-------|
| Admin IT | Budi Santoso | admin@test.com |
| Kepala | Dr. Hendra Wijaya | kepala@test.com |
| PPK | Rina Marlina, S.E. | ppk@test.com |
| Koordinator RT | Agus Setiawan | rt@test.com |
| Pegawai Internal | Dewi Kartika | pegawai@test.com |
| Peserta Eksternal | Muhammad Fauzi | eksternal@test.com |
| PIC (Testing) | Ahmad Rizki Pratama | pic@test.com |
| Peserta (Testing) | Siti Nurhaliza | peserta@test.com |

---

## 👥 Role & Menu Matrix

### 1. Admin IT (`admin@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Administrasi** (Section)
  - Manajemen User
  - Template Sertifikat
  - Log Sistem
- ✅ **Bimtek** (Section)
  - Kelola Bimtek
  - Laporan

**Testing:**
1. Login sebagai admin@test.com
2. Verify sidebar menampilkan semua menu di atas
3. Test akses ke setiap menu

---

### 2. Kepala Satuan Kerja (`kepala@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Persetujuan** (Section)
  - Pengajuan Bimtek
- ✅ **Bimtek** (Section)
  - Kelola Bimtek
  - Laporan

**Testing:**
1. Login sebagai kepala@test.com
2. Verify menu "Pengajuan Bimtek" untuk approval
3. Bisa melihat pengajuan yang perlu disetujui

---

### 3. PPK - Pejabat Pembuat Komitmen (`ppk@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Anggaran** (Section)
  - Persetujuan Anggaran
- ✅ **Bimtek** (Section)
  - Kelola Bimtek
  - Laporan

**Testing:**
1. Login sebagai ppk@test.com
2. Verify menu "Persetujuan Anggaran"
3. Bisa review dan approve anggaran pengajuan

---

### 4. Koordinator RT (`rt@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Rumah Tangga** (Section)
  - Kebutuhan Fasilitas

**Testing:**
1. Login sebagai rt@test.com
2. Verify menu "Kebutuhan Fasilitas"
3. Bisa kelola fasilitas & logistik untuk bimtek

---

### 5. Pegawai Internal (`pegawai@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Pengajuan** (Section)
  - Ajukan Bimtek
  - Pengajuan Saya
- ✅ **Bimtek** (Section)
  - Kelola Bimtek

**Testing:**
1. Login sebagai pegawai@test.com
2. Verify menu "Ajukan Bimtek" tersedia
3. Test buat pengajuan baru
4. Lihat status pengajuan di "Pengajuan Saya"

---

### 6. Peserta Eksternal (`eksternal@test.com`)
**Menu yang harus muncul:**
- ✅ Dashboard
- ✅ **Bimtek** (Section)
  - Kelola Bimtek
- ✅ **Aktivitas Saya** (Section)
  - Materi
  - Tugas
  - Absensi
  - Sertifikat

**Testing:**
1. Login sebagai eksternal@test.com
2. Hanya bisa lihat bimtek yang diikuti
3. Tidak ada menu "Ajukan Bimtek"
4. Bisa akses materi, tugas, absensi

---

## 🧪 Testing Steps per Role

### Admin IT - Full Access Test
```bash
# Login: admin@test.com / password
```
1. ✅ Buka menu "Manajemen User"
2. ✅ Coba buat user baru
3. ✅ Edit user existing
4. ✅ Buka "Template Sertifikat"
5. ✅ Upload/Edit template
6. ✅ Buka "Log Sistem"
7. ✅ Filter dan lihat activity logs

### Kepala - Approval Test
```bash
# Login: kepala@test.com / password
```
1. ✅ Buka "Pengajuan Bimtek"
2. ✅ Lihat daftar pengajuan yang menunggu
3. ✅ Review detail pengajuan
4. ✅ Test approve pengajuan
5. ✅ Test reject pengajuan dengan catatan

### PPK - Budget Approval Test
```bash
# Login: ppk@test.com / password
```
1. ✅ Buka "Persetujuan Anggaran"
2. ✅ Lihat pengajuan yang sudah disetujui Kepala
3. ✅ Review kebutuhan anggaran
4. ✅ Approve anggaran
5. ✅ Reject jika tidak sesuai

### Koordinator RT - Logistics Test
```bash
# Login: rt@test.com / password
```
1. ✅ Buka "Kebutuhan Fasilitas"
2. ✅ Lihat request fasilitas dari bimtek
3. ✅ Update status pemenuhan
4. ✅ Input detail fasilitas yang dipenuhi

### Pegawai Internal - Create Proposal Test
```bash
# Login: pegawai@test.com / password
```
1. ✅ Klik "Ajukan Bimtek"
2. ✅ Isi form pengajuan lengkap
3. ✅ Save as draft
4. ✅ Submit pengajuan
5. ✅ Lihat di "Pengajuan Saya"
6. ✅ Track status approval

### Peserta Eksternal - Participant Test
```bash
# Login: eksternal@test.com / password
```
1. ✅ Lihat bimtek yang diikuti
2. ✅ Download materi
3. ✅ Upload tugas
4. ✅ Isi absensi
5. ✅ Download sertifikat (jika sudah eligible)

---

## 🔍 Common Issues & Solutions

### Issue: Menu tidak muncul
**Solution:**
1. Check user punya `role_id` yang valid
2. Logout dan login ulang
3. Clear browser cache
4. Check method `isAdminIt()`, `isKepala()` dll di User model

### Issue: "Unknown" di role display
**Solution:**
```bash
php artisan db:seed --class=AssignRolesToUsersSeeder
```

### Issue: Forbidden/403 saat akses menu
**Solution:**
1. Check middleware di route
2. Verify user role sesuai dengan yang dibutuhkan
3. Check authorization policy

---

## 📝 Checklist Testing Semua Role

- [ ] **Admin IT** - Semua menu admin muncul dan berfungsi
- [ ] **Kepala** - Menu approval pengajuan muncul
- [ ] **PPK** - Menu approval anggaran muncul
- [ ] **Koordinator RT** - Menu kebutuhan fasilitas muncul
- [ ] **Pegawai Internal** - Menu ajukan bimtek & pengajuan saya muncul
- [ ] **Peserta Eksternal** - Menu aktivitas peserta muncul
- [ ] Semua role bisa akses Dashboard
- [ ] Role yang sesuai bisa akses menu Bimtek
- [ ] Authorization bekerja dengan benar (tidak bisa akses menu role lain)

---

## 🎯 Expected Behavior

### ✅ Correct Behavior:
- Admin IT: Bisa akses SEMUA menu
- Kepala: Hanya lihat menu approval & laporan
- PPK: Hanya lihat menu anggaran & laporan
- RT: Hanya lihat menu fasilitas
- Pegawai: Bisa ajukan bimtek & lihat pengajuan sendiri
- Eksternal: Hanya lihat bimtek yang diikuti

### ❌ Incorrect Behavior:
- Menu role lain muncul di sidebar
- Bisa akses URL menu role lain (403)
- Role "Unknown" di header
- Menu hilang setelah login

---

## 🚀 Quick Commands

```bash
# Start server
php artisan serve

# Seed all role users
php artisan db:seed --class=CompleteRoleUsersSeeder

# Fresh migration with all data
php artisan migrate:fresh --seed
php artisan db:seed --class=CompleteRoleUsersSeeder

# Check user roles
php artisan tinker
>>> User::with('role')->get(['name','email','role_id'])
```

---

**Server URL:** http://127.0.0.1:8000
