<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\LogSistem;
use App\Models\User;
use App\Models\BimtekPemateri;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BimtekController extends Controller
{
    /**
     * Display a listing of bimtek.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        // Mengapus pengajuan.user karena data pengaju kini melekat langsung di relasi pic
        $query = Bimtek::with(['pic', 'peserta', 'panitia']);

        // Admin IT, Kepala, PPK bisa melihat semua riwayat bimtek
        if (! $user->isAdminIt() && ! $user->isKepala() && ! $user->isPpk()) {
            // Pegawai/Peserta hanya melihat kelas di mana dia terlibat secara kontekstual
            $query->where(function ($q) use ($user) {
                $q->where('pic_user_id', $user->id)
                  ->orWhereHas('panitia', function ($subQ) use ($user) {
                      $subQ->where('user_id', $user->id);
                  })
                  ->orWhereHas('peserta', function ($subQ) use ($user) {
                      $subQ->where('user_id', $user->id);
                  });
            });
        }

        // Filter berdasarkan status alur (State Machine)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian judul atau lokasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_final', 'like', "%{$search}%")
                    ->orWhere('judul_rencana', 'like', "%{$search}%")
                    ->orWhere('lokasi_aktual', 'like', "%{$search}%");
            });
        }

        $bimteks = $query->latest()->paginate(10)->withQueryString();

        // Opsi disesuaikan dengan transisi status pelaksanaan pasca-approval
        $statusOptions = [
            'persiapan' => 'Persiapan',
            'registrasi' => 'Registrasi',
            'persiapan_selesai' => 'Persiapan Selesai',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return view('bimtek.index', compact('bimteks', 'statusOptions'));
    }

    /**
     * Display the specified bimtek.
     */
    public function show(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);
        $user = Auth::user();

        // LOGIKA OTOMATIS: Jika yang mengakses adalah PIC Utama dan statusnya masih 'disetujui_final',
        // langsung naikkan status ke 'persiapan' di database secara senyap.
        if ($bimtek->status === 'disetujui_final' && $bimtek->pic_user_id === $user->id) {
            $bimtek->update([
                'status' => 'persiapan'
            ]);
            
            // Mencatat perubahan ke log sistem agar tetap terlacak resmi
            if (class_exists(LogSistem::class)) {
                LogSistem::info("Status Bimtek ID {$bimtek->id} otomatis diubah dari disetujui_final ke persiapan saat diakses pertama kali oleh PIC.", $user->id);
            }
        }

        // Cek keterlibatan user di kelas ini melalui jembatan bimtek_pesertas
        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();

        if ($isPeserta && ! $user->isAdminIt()) {
            $bimtek->load([
                'materis' => fn($q) => $q->latest()->take(5),
                'tugas' => fn($q) => $q->orderBy('deadline')->take(5),
                'sesiAbsensis' => fn($q) => $q->latest()->take(5),
                'sertifikats' => fn($q) => $q->where('user_id', $user->id)->latest()->take(5),
            ]);

            // Ambil status berkas kelulusan langsung dari tabel bridge peserta
            $pivot = $bimtek->peserta()->where('user_id', $user->id)->first();
            $statusVerifikasi = $pivot?->pivot->status_verifikasi ?? 'invited';
            $isVerified = $statusVerifikasi === 'verified';

            return view('bimtek.show-peserta', compact('bimtek', 'isVerified', 'statusVerifikasi'));
        }

        // Full view manajemen untuk PIC, Panitia Struktural, dan Manajemen Terkait
        $bimtek->load([
            'pic',
            'panitia',
            'peserta',
            'materis',
            'tugas.pengumpulanTugas',
            'sesiAbsensis',
            'sertifikats.user',
            'fasilitasLogistiks',
            'syaratDokumens'
        ]);

        // Mengumpulkan daftar ID user yang sudah tergabung agar tidak muncul ganda di modal input
        $existingUserIds = array_merge(
            $bimtek->panitia->pluck('id')->toArray(),
            $bimtek->peserta->pluck('id')->toArray(),
            [$bimtek->pic_user_id]
        );

        $availableUsers = User::whereNotIn('id', $existingUserIds)
            ->whereHas('role', function ($q) {
                $q->where('nama_peran', 'Pegawai Internal');
            })
            ->orderBy('name')
            ->get();

        $canManage = $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isPic = $bimtek->pic_user_id === $user->id;

        return view('bimtek.show', compact('bimtek', 'availableUsers', 'canManage', 'isPic', 'isPeserta'));
    }

    /**
     * Generate invite code for a bimtek.
     */
    public function generateInviteCode(Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);
        if (! $bimtek->invite_code) {
            $bimtek->invite_code = Str::upper(Str::random(8));
            $bimtek->save();
        }

        return redirect()->route('bimtek.show', $bimtek)->with('success', 'Kode undangan berhasil dibuat. Silakan bagikan kode kepada para calon peserta.');
    }

    /**
     * Show the form for editing the specified bimtek.
     */
    public function edit(Bimtek $bimtek): View
    {
        $this->authorizePicPanitia($bimtek);
        $bimtek->load(['pic', 'panitia']);

        $statusOptions = [
            'persiapan' => 'Persiapan',
            'registrasi' => 'Registrasi',
            'persiapan_selesai' => 'Persiapan Selesai',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return view('bimtek.edit', compact('bimtek', 'statusOptions'));
    }

    /**
     * Update the specified bimtek.
     */
    public function update(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $statusLockResponse = $this->ensurePersiapanForDataChanges($bimtek);
        if ($statusLockResponse) {
            return $statusLockResponse;
        }

        $validated = $request->validate([
            'judul_final' => 'required|string|max:255',
            'lokasi_aktual' => 'nullable|string|max:255',
            'virtual_meeting_url' => 'nullable|url|max:500',
            'tanggal_mulai_aktual' => 'nullable|date',
            'tanggal_selesai_aktual' => 'nullable|date|after_or_equal:tanggal_mulai_aktual',
            'deskripsi_jadwal' => 'nullable|string',
            'status' => 'nullable|in:persiapan,registrasi,persiapan_selesai,berlangsung,selesai,dibatalkan',
            'anggaran_disetujui' => 'nullable|numeric|min:0',
            'syarat_kehadiran_persen' => 'nullable|integer|min:0|max:100',
            'syarat_tugas_persen' => 'nullable|integer|min:0|max:100',
            'has_tugas' => 'boolean',
            'has_sertifikat' => 'boolean',
            'syarat_tugas_wajib' => 'boolean',
            'daftar_pemateri' => 'nullable|array',
            'daftar_pemateri.*.nama' => 'nullable|string|max:255',
            'daftar_pemateri.*.asal_instansi' => 'nullable|string|max:255',
        ]);

        $validated['syarat_tugas_wajib'] = $request->boolean('syarat_tugas_wajib');
        $validated['has_tugas'] = $request->boolean('has_tugas');
        $validated['has_sertifikat'] = $request->boolean('has_sertifikat');

        // Mencegah bypass status ilegal lewat form input standar
        if (isset($validated['status']) && $validated['status'] !== $bimtek->status) {
            return back()->with('error', 'Perubahan status pelaksanaan hanya dapat dilakukan melalui tombol Kelola Status Bimtek.');
        }

        // Penyaringan modifikasi field major tata kelola anggaran asli DIPA
        $majorFields = ['anggaran_disetujui', 'syarat_kehadiran_persen', 'syarat_tugas_persen', 'syarat_tugas_wajib'];
        $changedMajorFields = [];
        foreach ($majorFields as $field) {
            if ($this->normalizeComparisonValue($bimtek->{$field}) !== $this->normalizeComparisonValue($validated[$field] ?? null)) {
                $changedMajorFields[] = $field;
            }
        }

        if (! empty($changedMajorFields)) {
            return back()->with('error', 'Perubahan data keuangan major tidak dapat dilakukan langsung. Gunakan fitur Revisi agar kembali ke alur persetujuan Kepala/PPK.');
        }

        unset($validated['status']);
        
        // Bersihkan data array narasumber
        $validated['daftar_pemateri'] = collect($request->input('daftar_pemateri', []))
            ->filter(fn($p) => ! empty($p['nama']))
            ->values()
            ->map(fn($p) => ['nama' => $p['nama'], 'asal_instansi' => $p['asal_instansi'] ?? null])
            ->all();

        $bimtek->update($validated);

        return redirect()->route('bimtek.show', $bimtek)->with('success', 'Data bimtek berhasil diperbarui.');
    }

    /**
     * Request revision - Melakukan transisi status langsung pada baris data Bimtek itu sendiri
     */
    public function requestRevisi(Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicOnly($bimtek);

        if ($bimtek->status !== 'disetujui_final') {
            return back()->with('error', 'Revisi hanya dapat diajukan setelah pengajuan disetujui final.');
        }

        // Menurunkan state alur kembali ke revisi agar form pengajuan terbuka untuk PIC
        $bimtek->update([
            'status' => 'perlu_revisi',
            'catatan_kepala' => null,
            'catatan_ppk' => null,
            'kepala_approved_at' => null,
            'ppk_approved_at' => null
        ]);

        return redirect()
            ->route('pengajuan.edit', $bimtek->id)
            ->with('success', 'Status diturunkan ke Perlu Revisi. Silakan perbarui rancangan anggaran biaya Anda.');
    }
    /**
     * Menyimpan data pemateri baru ke dalam kelas Bimtek
     */
    public function storePemateri(Request $request, Bimtek $bimtek)
    {
        $request->validate([
            'nama_pemateri' => 'required|string|max:255',
            'asal_instansi' => 'required|string|max:255',
        ]);

        $bimtek->pemateris()->create($request->only(['nama_pemateri', 'asal_instansi']));

        return redirect()->back()->with('success', 'Data pemateri berhasil ditambahkan.');
    }

    /**
     * Menghapus data pemateri dari kelas Bimtek
     */
    public function destroyPemateri(Bimtek $bimtek, BimtekPemateri $pemateri)
    {
        // Pastikan pemateri yang dihapus memang milik bimtek yang sedang dibuka
        if ($pemateri->bimtek_id !== $bimtek->id) {
            abort(403);
        }

        $pemateri->delete();

        return redirect()->back()->with('success', 'Data pemateri berhasil dihapus.');
    }
    /**
     * Assign Panitia to bimtek (Menembak tabel bridge riil `bimtek_panitias`).
     */
    public function assignPanitia(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicOnly($bimtek);

        $statusLockResponse = $this->ensurePersiapanForDataChanges($bimtek);
        if ($statusLockResponse) {
            return $statusLockResponse;
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'fungsi_panitia' => 'required|string|max:100',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (! $user->isPegawaiInternal()) {
            return back()->with('error', 'Hanya pegawai internal BBPMP yang dapat ditugaskan sebagai panitia.');
        }

        if ($bimtek->panitia()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'Pegawai tersebut sudah terdaftar sebagai panitia di kelas ini.');
        }

        // Batasan jumlah panitia maks 10% sesuai regulasi DIPA BBPMP Sumbar
        $jumlahPeserta = $bimtek->jumlah_peserta ?? 0;
        if ($jumlahPeserta <= 0) {
            return back()->with('error', 'Mohon tentukan perkiraan target jumlah peserta terlebih dahulu.');
        }

        $maxPanitia = max(1, (int) ceil($jumlahPeserta * 0.10));
        if ($bimtek->panitia()->count() >= $maxPanitia) {
            return back()->with('error', "Kuota kepanitiaan penuh! Maksimum panitia untuk kegiatan ini adalah {$maxPanitia} orang.");
        }

        // Jaring pengaman: Cabut dari peserta jika ada, lalu masukkan ke panitia
        $bimtek->peserta()->detach($validated['user_id']);
        $bimtek->panitia()->attach($validated['user_id'], [
            'fungsi_panitia' => $validated['fungsi_panitia'],
        ]);

        return back()->with('success', "{$user->name} sukses didelegasikan sebagai Panitia.");
    }

    /**
     * Update status pelaksanaan (State Machine Transitions).
     */
    public function updateStatus(Request $request, Bimtek $bimtek): RedirectResponse
    {
        // 1. Otorisasi Keamanan (Tetap dipertahankan dari kode Anda sebelumnya)
        $this->authorizePicPanitia($bimtek);

        $targetStatus = $request->input('status');
        
        $validated = $request->validate([
            'status' => 'required|in:registrasi,persiapan_selesai,berlangsung,selesai,dibatalkan',
        ]);

        $targetStatus = $validated['status'];
        
        $oldStatus = $bimtek->status;

        if ($targetStatus === $oldStatus) {
            return back()->with('success', 'Status tidak berubah.');
        }

        // 2. Proteksi Ekstra Pembatalan: Hanya PIC Utama (Tetap dipertahankan)
        if ($targetStatus === 'dibatalkan' && $bimtek->pic_user_id !== Auth::id()) {
            return back()->with('error', 'Hanya PIC utama yang memiliki wewenang membatalkan kegiatan.');
        }

        $check = ['allowed' => true, 'errors' => []];

        // 3. Arahkan ke gerbang validasi Model (State Machine) yang baru kita buat
        switch ($targetStatus) {
            case 'registrasi':
                $check = $bimtek->canOpenRegistration();
                break;
            case 'persiapan_selesai': 
                $check = $bimtek->canCompletePreparation();
                break;
            case 'berlangsung':
                $check = $bimtek->canStartEvent();
                break;
            case 'selesai':
                $check = $bimtek->canFinishEvent();
                break;
            case 'dibatalkan':
                $check = $bimtek->canCancel();
                break;
        }

        // 4. Jika Model menolak perubahan status karena syarat tidak lengkap
        if (!$check['allowed']) {
            // Lewatkan array errors langsung agar bisa diloop menggunakan tag <li> di Blade
            return back()->with('error_validasi', $check['errors']);
        }

        // 5. Lolos validasi, eksekusi perubahan status
        $bimtek->update(['status' => $targetStatus]);

        // 6. Pencatatan Log Sistem (Tetap dipertahankan)
        if (class_exists(LogSistem::class)) {
            LogSistem::info("Status Bimtek ID {$bimtek->id} diubah dari {$oldStatus} ke {$targetStatus} menggunakan State Machine", Auth::id());
        }

        return back()->with('success', "Status sukses diperbarui ke tahap " . strtoupper(str_replace('_', ' ', $targetStatus)) . ".");
    }

    /**
     * Upload Surat Undangan (Single Workflow - Mengisi kolom tunggal `file_surat_undangan_path`)
     */
    public function uploadDraft(Request $request, Bimtek $bimtek): RedirectResponse
    {
        // 1. Otorisasi Keamanan
        $this->authorizePicPanitia($bimtek);

        // 2. Proteksi State Machine (Mencegah API Bypass)
        if (!in_array($bimtek->status, ['persiapan', 'registrasi', 'persiapan_selesai'])) {
            return back()->with('error', 'Gagal: Dokumen surat undangan hanya dapat diunggah atau diubah pada tahap Persiapan, Registrasi, atau Persiapan Selesai.');
        }

        // 3. Validasi File (Hanya PDF, maks 5MB)
        $request->validate([
            'surat_draft' => 'required|file|mimes:pdf|max:5120'
        ]);

        // 4. Hapus dokumen lama jika ada untuk menghemat storage
        if ($bimtek->file_surat_undangan_path) {
            
            Storage::disk('public')->delete($bimtek->file_surat_undangan_path);
        }

        // 5. Eksekusi Upload dan Pembaruan Database
        $path = $request->file('surat_draft')->store('surat-undangan', 'public');
        
        $bimtek->update([
            'file_surat_undangan_path' => $path,
            'file_surat_undangan_uploaded_by' => Auth::id(),
            'file_surat_undangan_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Surat undangan resmi berhasil diterbitkan ke sistem.');
    }


    /**
     * Preview & Download Dokumen Surat Undangan Tunggal
     */
    public function previewDraft(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (! $bimtek->file_surat_undangan_path || ! Storage::disk('public')->exists($bimtek->file_surat_undangan_path)) {
            return back()->with('error', 'Berkas fisik surat undangan belum diunggah.');
        }

        // PERBAIKAN: Fallback ke judul_rencana jika judul_final belum diisi oleh panitia
        $namaJudul = $bimtek->judul_final ?? $bimtek->judul_rencana ?? 'Kegiatan Bimtek';

        return response()->file(Storage::disk('public')->path($bimtek->file_surat_undangan_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Surat Undangan - ' . $namaJudul . '.pdf"',
        ]);
    }

    public function downloadDraft(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (! $bimtek->file_surat_undangan_path || ! Storage::disk('public')->exists($bimtek->file_surat_undangan_path)) {
            return back()->with('error', 'Berkas fisik surat undangan tidak ditemukan.');
        }

        // PERBAIKAN: Fallback ke judul_rencana jika judul_final belum diisi oleh panitia
        $namaJudul = $bimtek->judul_final ?? $bimtek->judul_rencana ?? 'Kegiatan Bimtek';

        return Storage::disk('public')->download($bimtek->file_surat_undangan_path, "Surat Undangan - {$namaJudul}.pdf");
    }


    /**
     * Add Peserta (Menyuntikkan baris data langsung ke tabel bridge `bimtek_pesertas`)
     */
    public function addPeserta(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'peran_kontekstual' => 'required|in:pic,panitia,peserta',
        ]);

        // Jika form frontend meminta pendaftaran panitia, bypass jalurnya ke fungsi assignPanitia
        if ($validated['peran_kontekstual'] === 'panitia') {
            $request->merge(['fungsi_panitia' => 'Anggota Tim Pelaksana']);
            return $this->assignPanitia($request, $bimtek);
        }

        // 1. PROTEKSI STATE MACHINE (Cegah Penambahan Peserta di Luar Jalur BPMN)
        if (!in_array($bimtek->status, ['persiapan', 'registrasi'])) {
            return back()->with('error', 'Gagal: Penambahan peserta manual oleh panitia hanya diizinkan pada fase Persiapan dan Registrasi.');
        }

        // 2. Cegah Duplikasi
        if ($bimtek->peserta()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'User sudah terdaftar sebagai peserta di kelas ini.');
        }

        // 3. Set Status Verifikasi & Eksekusi Relasi
        $pivotData = [
            'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified',
        ];

        $bimtek->peserta()->attach($validated['user_id'], $pivotData);
        $user = \App\Models\User::find($validated['user_id']); // Gunakan namespace eksplisit jika belum di-import

        // 4. Logika Pengiriman Email Notifikasi Otomatis
        try {
        
            Mail::to($user->email)->send(new \App\Mail\PesertaAddedToBimtekMail($bimtek, $user, route('login')));
        } catch (\Exception $e) {
            // Mencegah crash jika mail server lokal (Mailpit/SMTP) belum di-setup di file .env
        }

        return back()->with('success', "{$user->name} berhasil didaftarkan sebagai peserta kegiatan.");
    }

    public function removePeserta(Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);
        $bimtek->peserta()->detach($user->id);
        $bimtek->panitia()->detach($user->id);

        return back()->with('success', "Aktor berhasil dikeluarkan dari kegiatan.");
    }
    
    public function toggleTugas(Request $request, Bimtek $bimtek)
    {
        // Kunci Validasi: Hanya bisa diubah saat fase persiapan
        if (!$bimtek->isEditable()) {
            return back()->with('error', 'Konfigurasi penugasan tidak dapat diubah karena kelas sudah melewati fase persiapan.');
        }

        // Ambil nilai dari input hidden alpine.js (1 atau 0)
        $hasTugas = $request->input('has_tugas') == 1 ? true : false;

        $bimtek->update([
            'has_tugas' => $hasTugas
        ]);

        $pesan = $hasTugas 
            ? 'Konfigurasi disimpan: Kegiatan ini sekarang mewajibkan Penugasan.' 
            : 'Konfigurasi disimpan: Kegiatan ini tidak memiliki Penugasan.';

        return back()->with('success', $pesan);
    }
    /**
     * Access Gate Protections (Refaktorisasi Basis Relasi Terpisah)
     */
    protected function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();

        if (! $isPic && ! $isPanitia && ! $isPeserta) {
            abort(403, 'Anda tidak diizinkan masuk ke halaman kelas bimtek ini.');
        }
    }

    protected function authorizePicPanitia(Bimtek $bimtek): void
    {
        $user = Auth::user();
        if ($user->isAdminIt()) {
            abort(403, 'Akses ditolak! Akun Admin IT hanya memiliki hak read-only pada modul kelas.');
        }

        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Wewenang khusus ini terbatas hanya untuk PIC Kegiatan atau Panitia Struktural.');
        }
    }

    protected function authorizePicOnly(Bimtek $bimtek): void
    {
        if ($bimtek->pic_user_id !== Auth::id()) {
            abort(403, 'Aksi penunjukan hak akses tingkat tinggi ini hanya dapat dieksekusi oleh PIC Utama.');
        }
    }

    protected function ensurePersiapanForDataChanges(Bimtek $bimtek): ?RedirectResponse
    {
        if (!$bimtek->isEditable()) {
            return back()->with('error', 'Perubahan modifikasi logistik dan data internal hanya diizinkan saat status Persiapan.');
        }
        return null;
    }

    protected function normalizeComparisonValue(mixed $value): string
    {
        if (is_bool($value)) return $value ? '1' : '0';
        return ($value === null || $value === '') ? '' : (string) $value;
    }
}