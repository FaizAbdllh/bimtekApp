<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\LogSistem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BimtekController extends Controller
{
    /**
     * Display a listing of bimtek.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Bimtek::with(['pengajuan.user', 'pic', 'peserta']);

        // Admin IT, Kepala, PPK bisa lihat semua bimtek
        // User lain hanya lihat bimtek yang dia terlibat
        if (!$user->isAdminIt() && !$user->isKepala() && !$user->isPpk()) {
            // User lihat bimtek yang:
            // 1. Dia terlibat sebagai PIC/Panitia/Peserta/Pemateri, ATAU
            // 2. Dia adalah pengaju dari bimtek tersebut
            $query->where(function ($q) use ($user) {
                $q->whereHas('users', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                })->orWhereHas('pengajuan', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_pelaksanaan', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_final', 'like', "%{$search}%")
                  ->orWhere('lokasi_aktual', 'like', "%{$search}%");
            });
        }

        $bimteks = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'persiapan' => 'Persiapan',
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
        
        // Check if user is peserta - redirect to simplified view
        $isPeserta = $bimtek->peserta()->where('users.id', $user->id)->exists();
        
        if ($isPeserta && !$user->isAdminIt()) {
            // Load only necessary data for peserta view
            $bimtek->load([
                'materis' => function ($q) {
                    $q->latest()->take(5);
                },
                'tugas' => function ($q) {
                    $q->orderBy('deadline')->take(5);
                },
                'sesiAbsensis' => function ($q) {
                    $q->latest()->take(5);
                },
                'sertifikats' => function ($q) use ($user) {
                    $q->where('user_id', $user->id)->latest()->take(5);
                },
            ]);
            
            // Get verification status for this peserta
            $pivot = $bimtek->users()
                ->where('users.id', $user->id)
                ->where('bimtek_user.peran_kontekstual', 'peserta')
                ->first();
            
            $statusVerifikasi = $pivot?->pivot->status_verifikasi ?? 'invited';
            $isVerified = $statusVerifikasi === 'verified';
            
            return view('bimtek.show-peserta', compact('bimtek', 'isVerified', 'statusVerifikasi'));
        }

        // Full view for PIC, Panitia, Admin, etc.
        $bimtek->load([
            'pengajuan.user',
            'pengajuan.fasilitasLogistiks',
            'pic',
            'panitia',
            // 'pemateri', // dihapus karena bukan relasi
            'peserta',
            'materis',
            'tugas.pengumpulanTugas',
            'sesiAbsensis',
            'sertifikats.user',
        ]);

        // Get available users untuk modal assign
        $existingUserIds = $bimtek->users->pluck('id')->toArray();
        $availableUsers = User::whereNotIn('id', $existingUserIds)
            ->whereHas('role', function ($q) {
                // Hanya Pegawai Internal yang boleh dijadikan panitia
                $q->where('nama_peran', 'Pegawai Internal');
            })
            ->orderBy('name')
            ->get();

        // Check if user can manage (PIC or Panitia)
        $canManage = $bimtek->pic_user_id === $user->id ||
                     $bimtek->panitia()->where('users.id', $user->id)->exists();
        
        // Check if user is PIC (for assigning panitia)
        $isPic = $bimtek->pic_user_id === $user->id;

        return view('bimtek.show', compact('bimtek', 'availableUsers', 'canManage', 'isPic', 'isPeserta'));
    }

    /**
     * Show the form for editing the specified bimtek.
     */
    public function edit(Bimtek $bimtek): View
    {
        $this->authorizePicPanitia($bimtek);

        $bimtek->load(['pengajuan.user', 'pic', 'panitia']);

        $statusOptions = [
            'persiapan' => 'Persiapan',
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
            'tanggal_mulai_aktual' => 'nullable|date',
            'tanggal_selesai_aktual' => 'nullable|date|after_or_equal:tanggal_mulai_aktual',
            'deskripsi_jadwal' => 'nullable|string',
            'status_pelaksanaan' => 'nullable|in:persiapan,berlangsung,selesai,dibatalkan',
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

        // Status pelaksanaan wajib melalui endpoint workflow agar transisi tervalidasi.
        if (($validated['status_pelaksanaan'] ?? $bimtek->status_pelaksanaan) !== $bimtek->status_pelaksanaan) {
            $this->logRejectedAction(
                $bimtek,
                'Perubahan status pelaksanaan melalui form edit ditolak.',
                [
                    'status_lama' => $bimtek->status_pelaksanaan,
                    'status_diminta' => $validated['status_pelaksanaan'] ?? null,
                ]
            );
            return back()->with('error', 'Perubahan status pelaksanaan hanya dapat dilakukan melalui tombol Kelola Status Bimtek.');
        }

        $majorFields = [
            'anggaran_disetujui',
            'syarat_kehadiran_persen',
            'syarat_tugas_persen',
            'syarat_tugas_wajib',
        ];

        $majorFieldLabels = [
            'anggaran_disetujui' => 'Anggaran Disetujui',
            'syarat_kehadiran_persen' => 'Syarat Kehadiran',
            'syarat_tugas_persen' => 'Nilai Minimal Tugas',
            'syarat_tugas_wajib' => 'Wajib Kumpul Tugas',
        ];

        $changedMajorFields = [];
        foreach ($majorFields as $field) {
            if ($this->normalizeComparisonValue($bimtek->{$field}) !== $this->normalizeComparisonValue($validated[$field] ?? null)) {
                $changedMajorFields[] = $majorFieldLabels[$field] ?? $field;
            }
        }

        if (!empty($changedMajorFields)) {
            $this->logRejectedAction(
                $bimtek,
                'Perubahan field major secara langsung ditolak.',
                ['field_major' => implode(', ', $changedMajorFields)]
            );
            return back()->with('error', 'Perubahan major tidak dapat dilakukan langsung: ' . implode(', ', $changedMajorFields) . '. Gunakan fitur Revisi agar kembali ke alur persetujuan Kepala dan PPK.');
        }

        unset($validated['status_pelaksanaan']);
        $validated['daftar_pemateri'] = collect($request->input('daftar_pemateri', []))
            ->filter(function ($pemateri) {
                return !empty($pemateri['nama']);
            })
            ->values()
            ->map(function ($pemateri) {
                return [
                    'nama' => $pemateri['nama'],
                    'asal_instansi' => $pemateri['asal_instansi'] ?? null,
                ];
            })
            ->all();
        $bimtek->update($validated);

        return redirect()
            ->route('bimtek.show', $bimtek)
            ->with('success', 'Data bimtek berhasil diperbarui.');
    }


    /**
     * Update daftar pemateri bimtek.
     */
    public function updatePemateri(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $statusLockResponse = $this->ensurePersiapanForDataChanges($bimtek);
        if ($statusLockResponse) {
            return $statusLockResponse;
        }

        $request->validate([
            'daftar_pemateri' => 'nullable|array',
            'daftar_pemateri.*.nama' => 'nullable|string|max:255',
            'daftar_pemateri.*.asal_instansi' => 'nullable|string|max:255',
        ]);

        $pemateri = collect($request->input('daftar_pemateri', []))
            ->filter(function ($pemateri) {
                return !empty($pemateri['nama']);
            })
            ->values()
            ->map(function ($pemateri) {
                return [
                    'nama' => $pemateri['nama'],
                    'asal_instansi' => $pemateri['asal_instansi'] ?? null,
                ];
            })
            ->all();

        $bimtek->update(['daftar_pemateri' => $pemateri]);

        return back()->with('success', 'Daftar pemateri berhasil diperbarui.');
    }

    /**
     * Request revision of the approved pengajuan by PIC.
     */
    public function requestRevisi(Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicOnly($bimtek);

        if (!$bimtek->pengajuan) {
            return back()->with('error', 'Pengajuan tidak ditemukan untuk bimtek ini.');
        }

        if ($bimtek->pengajuan->status_pengajuan !== 'disetujui_final') {
            return back()->with('error', 'Revisi hanya dapat diajukan setelah pengajuan disetujui final.');
        }

        $bimtek->pengajuan->update([
            'status_pengajuan' => 'perlu_revisi',
            'catatan_kepala' => null,
            'catatan_ppk' => null,
            'kepala_approved_at' => null,
        ]);

        return redirect()
            ->route('pengajuan.edit', $bimtek->pengajuan)
            ->with('success', 'Pengajuan dikembalikan untuk direvisi. Silakan perbarui data dan ajukan ulang.');
    }

    /**
     * Assign Panitia to bimtek.
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

        $user = User::with('role')->findOrFail($validated['user_id']);

        if (!$user->isPegawaiInternal()) {
            return back()->with('error', 'Hanya user dengan role Pegawai Internal yang dapat ditambahkan sebagai panitia.');
        }

        // Check if user already assigned as Panitia
        if ($bimtek->panitia()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'User sudah menjadi Panitia di bimtek ini.');
        }

        // Enforce panitia cap: max 10% dari jumlah peserta yang diajukan (minimal 1)
        $jumlahPeserta = $bimtek->pengajuan?->jumlah_peserta ?? null;
        if (empty($jumlahPeserta)) {
            return back()->with('error', 'Mohon isi Estimasi Jumlah Peserta di Pengajuan terlebih dahulu sebelum menambahkan Panitia.');
        }

        $maxPanitia = max(1, (int) ceil($jumlahPeserta * 0.10));
        $currentPanitia = $bimtek->panitia()->count();
        if ($currentPanitia >= $maxPanitia) {
            return back()->with('error', "Jumlah Panitia sudah mencapai batas maksimum ({$maxPanitia}) berdasarkan estimasi peserta.");
        }

        // Remove from other roles if exists, then add as Panitia
        $bimtek->users()->detach($validated['user_id']);
        $bimtek->users()->attach($validated['user_id'], [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'panitia',
            'fungsi_panitia' => $validated['fungsi_panitia'],
        ]);

        return back()->with('success', "{$user->name} berhasil ditambahkan sebagai Panitia ({$validated['fungsi_panitia']}).");
    }

    /**
     * Update status pelaksanaan.
     */
    public function updateStatus(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'status_pelaksanaan' => 'required|in:persiapan,berlangsung,selesai,dibatalkan',
        ]);

        $oldStatus = $bimtek->status_pelaksanaan;

        $allowedTransitions = [
            'persiapan' => ['berlangsung', 'dibatalkan'],
            'berlangsung' => ['persiapan', 'selesai'],
            'selesai' => [],
            'dibatalkan' => [],
        ];

        $newStatus = $validated['status_pelaksanaan'];
        if ($newStatus === $oldStatus) {
            return back()->with('success', 'Status bimtek tidak berubah.');
        }

        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [], true)) {
            $this->logRejectedAction(
                $bimtek,
                'Transisi status tidak valid ditolak.',
                [
                    'status_lama' => $oldStatus,
                    'status_diminta' => $newStatus,
                ]
            );
            return back()->with('error', 'Transisi status tidak valid dari ' . $oldStatus . ' ke ' . $newStatus . '.');
        }

        // Batalkan bimtek dibatasi ke PIC untuk kontrol governance yang lebih ketat.
        if ($newStatus === 'dibatalkan' && $bimtek->pic_user_id !== Auth::id()) {
            $this->logRejectedAction(
                $bimtek,
                'Aksi batalkan bimtek ditolak karena bukan PIC.',
                [
                    'status_lama' => $oldStatus,
                    'status_diminta' => $newStatus,
                ]
            );
            return back()->with('error', 'Hanya PIC yang dapat membatalkan bimtek.');
        }

        $bimtek->update($validated);

        $statusLabels = [
            'persiapan' => 'Persiapan',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        // Log perubahan status bimtek
        $user = Auth::user();
        $pesan = sprintf(
            'Status bimtek "%s" (ID: %d) diubah dari "%s" menjadi "%s" oleh %s',
            $bimtek->judul_final ?? '-',
            $bimtek->id,
            $statusLabels[$oldStatus] ?? $oldStatus,
            $statusLabels[$validated['status_pelaksanaan']] ?? $validated['status_pelaksanaan'],
            $user ? ($user->name . ' (' . $user->email . ')') : 'Sistem'
        );
        LogSistem::info($pesan, $user ? $user->id : null);

        return back()->with('success', "Status bimtek berhasil diubah menjadi {$statusLabels[$validated['status_pelaksanaan']]}.");
    }

    /**
     * Upload surat draft (oleh PIC/Panitia).
     */
    public function uploadDraft(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $statusLockResponse = $this->ensurePersiapanForDataChanges($bimtek);
        if ($statusLockResponse) {
            return $statusLockResponse;
        }

        $user = Auth::user();
        $validated = $request->validate([
            'surat_draft' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Delete old draft if exists
        if ($bimtek->file_surat_draft_path) {
            Storage::disk('public')->delete($bimtek->file_surat_draft_path);
        }

        $path = $request->file('surat_draft')->store('surat-draft', 'public');
        $bimtek->update([
            'file_surat_draft_path' => $path,
            'file_surat_draft_uploaded_by' => $user ? $user->id : null,
            'file_surat_draft_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Draft surat undangan berhasil diupload.');
    }

    /**
     * Upload surat final (oleh Persuratan).
     */
    public function uploadFinal(Request $request, Bimtek $bimtek): RedirectResponse
    {
        // Only Persuratan can upload final
        if (!Auth::user()->isPersuratan()) {
            abort(403, 'Hanya Bagian Persuratan yang dapat mengunggah surat final.');
        }

        $user = Auth::user();
        $validated = $request->validate([
            'surat_final' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Delete old final if exists
        if ($bimtek->file_surat_final_path) {
            Storage::disk('public')->delete($bimtek->file_surat_final_path);
        }

        $path = $request->file('surat_final')->store('surat-final', 'public');
        $bimtek->update([
            'file_surat_final_path' => $path,
            'file_surat_final_uploaded_by' => $user ? $user->id : null,
            'file_surat_final_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Surat undangan final berhasil diupload oleh Bagian Persuratan.');
    }

    /**
     * Preview surat draft (inline PDF view).
     */
    public function previewDraft(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (!$bimtek->file_surat_draft_path || !Storage::disk('public')->exists($bimtek->file_surat_draft_path)) {
            return back()->with('error', 'File surat draft tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($bimtek->file_surat_draft_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Surat Undangan Draft - ' . $bimtek->judul_final . '.pdf"'
        ]);
    }

    /**
     * Download surat draft.
     */
    public function downloadDraft(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (!$bimtek->file_surat_draft_path || !Storage::disk('public')->exists($bimtek->file_surat_draft_path)) {
            return back()->with('error', 'File surat draft tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $bimtek->file_surat_draft_path, 
            'Surat Undangan Draft - ' . $bimtek->judul_final . '.pdf'
        );
    }

    /**
     * Preview surat final (inline PDF view).
     */
    public function previewFinal(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (!$bimtek->file_surat_final_path || !Storage::disk('public')->exists($bimtek->file_surat_final_path)) {
            return back()->with('error', 'File surat final belum tersedia.');
        }

        return response()->file(Storage::disk('public')->path($bimtek->file_surat_final_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Surat Undangan Final - ' . $bimtek->judul_final . '.pdf"'
        ]);
    }

    /**
     * Download surat final.
     */
    public function downloadFinal(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        if (!$bimtek->file_surat_final_path || !Storage::disk('public')->exists($bimtek->file_surat_final_path)) {
            return back()->with('error', 'File surat final belum tersedia.');
        }

        return Storage::disk('public')->download(
            $bimtek->file_surat_final_path, 
            'Surat Undangan Final - ' . $bimtek->judul_final . '.pdf'
        );
    }

    /**
     * Manage peserta for bimtek.
     */
    public function peserta(Bimtek $bimtek): View
    {
        $this->authorizePicPanitia($bimtek);

        $bimtek->load(['peserta', 'pemateri', 'panitia', 'pic']);

        // Get available users (exclude those already in bimtek)
        $existingUserIds = $bimtek->users->pluck('id')->toArray();
        $availableUsers = User::whereNotIn('id', $existingUserIds)
            ->orderBy('name')
            ->get();

        return view('bimtek.peserta', compact('bimtek', 'availableUsers'));
    }

    /**
     * Add user to bimtek.
     */
    public function addPeserta(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'peran_kontekstual' => 'required|in:pic,panitia,pemateri,peserta',
        ]);

        // Check if user already in bimtek
        if ($bimtek->users()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'User sudah terdaftar di bimtek ini.');
        }

        // If adding as Panitia, enforce cap based on jumlah_peserta
        if (($validated['peran_kontekstual'] ?? '') === 'panitia') {
            $jumlahPeserta = $bimtek->pengajuan?->jumlah_peserta ?? null;
            if (empty($jumlahPeserta)) {
                return back()->with('error', 'Mohon isi Estimasi Jumlah Peserta di Pengajuan terlebih dahulu sebelum menambahkan Panitia.');
            }
            $maxPanitia = max(1, (int) ceil($jumlahPeserta * 0.10));
            $currentPanitia = $bimtek->panitia()->count();
            if ($currentPanitia >= $maxPanitia) {
                return back()->with('error', "Jumlah Panitia sudah mencapai batas maksimum ({$maxPanitia}) berdasarkan estimasi peserta.");
            }
        }

        $bimtek->users()->attach($validated['user_id'], [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => $validated['peran_kontekstual'],
        ]);

        $user = User::find($validated['user_id']);
        $peranLabel = [
            'pic' => 'PIC',
            'panitia' => 'Panitia',
            'pemateri' => 'Pemateri',
            'peserta' => 'Peserta',
        ];

        return back()->with('success', "{$user->name} berhasil ditambahkan sebagai {$peranLabel[$validated['peran_kontekstual']]}.");
    }

    /**
     * Remove user from bimtek.
     */
    public function removePeserta(Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $bimtek->users()->detach($user->id);

        return back()->with('success', "{$user->name} berhasil dihapus dari bimtek.");
    }

    /**
     * Update user role in bimtek.
     */
    public function updatePeran(Request $request, Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'peran_kontekstual' => 'required|in:pic,panitia,pemateri,peserta',
        ]);

        $bimtek->users()->updateExistingPivot($user->id, [
            'peran_kontekstual' => $validated['peran_kontekstual'],
        ]);

        return back()->with('success', "Peran {$user->name} berhasil diubah.");
    }

    /**
     * Check if user has access to view bimtek.
     */
    protected function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Admin IT, Kepala, PPK, dan Persuratan dapat melihat semua bimtek
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk() || $user->isPersuratan()) {
            return;
        }

        // Check if user is involved in bimtek
        if (!$bimtek->users()->where('user_id', $user->id)->exists()) {
            // Check if user is the pengajuan owner
            if ($bimtek->pengajuan && $bimtek->pengajuan->user_id === $user->id) {
                return;
            }
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }
    }

    /**
     * Check if user is PIC or Panitia.
     */
    protected function authorizePicPanitia(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Admin IT tidak boleh mengelola (read-only access)
        if ($user->isAdminIt()) {
            $this->logRejectedAction($bimtek, 'Akses kelola bimtek ditolak untuk Admin IT.');
            abort(403, 'Admin IT hanya memiliki akses read-only. Tidak dapat mengelola bimtek.');
        }

        // Check if user is PIC or Panitia
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        if (!$isPic && !$isPanitia) {
            $this->logRejectedAction($bimtek, 'Akses kelola bimtek ditolak karena bukan PIC/Panitia.');
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola bimtek ini.');
        }
    }

    /**
     * Check if user is PIC only (for assigning panitia).
     */
    protected function authorizePicOnly(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Admin IT tidak boleh mengelola (read-only access)
        if ($user->isAdminIt()) {
            $this->logRejectedAction($bimtek, 'Akses kelola panitia ditolak untuk Admin IT.');
            abort(403, 'Admin IT hanya memiliki akses read-only. Tidak dapat mengelola bimtek.');
        }

        // Check if user is PIC
        $isPic = $bimtek->pic_user_id === $user->id;

        if (!$isPic) {
            $this->logRejectedAction($bimtek, 'Aksi khusus PIC ditolak karena user bukan PIC.');
            abort(403, 'Hanya PIC yang dapat menambahkan Panitia.');
        }
    }

    /**
     * Normalize values before strict comparison for governance checks.
     */
    protected function normalizeComparisonValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null || $value === '') {
            return '';
        }

        return (string) $value;
    }

    /**
     * Lock non-status data changes when bimtek is not in persiapan stage.
     */
    protected function ensurePersiapanForDataChanges(Bimtek $bimtek): ?RedirectResponse
    {
        if ($bimtek->status_pelaksanaan !== 'persiapan') {
            $this->logRejectedAction(
                $bimtek,
                'Perubahan data ditolak karena status bukan Persiapan.',
                ['status_aktual' => $bimtek->status_pelaksanaan]
            );
            return back()->with('error', 'Perubahan data bimtek hanya diizinkan saat status Persiapan.');
        }

        return null;
    }

    /**
     * Record governance-related rejected actions for audit trail.
     */
    protected function logRejectedAction(Bimtek $bimtek, string $message, array $context = []): void
    {
        $user = Auth::user();

        $base = sprintf(
            '%s Bimtek "%s" (ID: %d). User: %s',
            $message,
            $bimtek->judul_final ?? '-',
            $bimtek->id,
            $user ? ($user->name . ' (' . $user->email . ')') : 'Tidak diketahui'
        );

        if (!empty($context)) {
            $base .= ' | Context: ' . json_encode($context);
        }

        LogSistem::warning($base, $user?->id);
    }
}
