<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends Controller
{
    /**
     * Display a listing of tugas for a bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);

        $bimtek->load(['tugas' => function ($q) {
            $q->withCount('pengumpulanTugas')->latest();
        }]);

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);
        $user = Auth::user();

        // Check verification status for peserta
        $isVerified = false;
        if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
            $pivot = $bimtek->users()
                ->where('users.id', $user->id)
                ->where('bimtek_user.peran_kontekstual', 'peserta')
                ->first();
            $statusVerifikasi = $pivot?->pivot->status_verifikasi ?? 'invited';
            $isVerified = $statusVerifikasi === 'verified';
        } else {
            $isVerified = true; // Non-peserta or no verification required
        }

        // Get user's submissions if peserta
        $userSubmissions = [];
        if ($isPeserta) {
            $userSubmissions = PengumpulanTugas::where('user_id', $user->id)
                ->whereIn('tugas_id', $bimtek->tugas->pluck('id'))
                ->get()
                ->keyBy('tugas_id');
        }

        return view('tugas.index', compact('bimtek', 'canManage', 'isPeserta', 'userSubmissions', 'isVerified'));
    }

    /**
     * Show the form for creating a new tugas.
     */
    public function create(Bimtek $bimtek): View
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);

        return view('tugas.create', compact('bimtek'));
    }

    /**
     * Store a newly created tugas.
     */
    public function store(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'file_instruksi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:20480',
        ], [
            'judul.required' => 'Judul tugas wajib diisi.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after' => 'Deadline harus setelah waktu sekarang.',
            'file_instruksi.mimes' => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'file_instruksi.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // Upload file if exists
        $filePath = null;
        if ($request->hasFile('file_instruksi')) {
            $filePath = $request->file('file_instruksi')->store("tugas/bimtek-{$bimtek->id}", 'public');
        }

        Tugas::create([
            'bimtek_id' => $bimtek->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'deadline' => $validated['deadline'],
            'file_instruksi_path' => $filePath,
        ]);

        return redirect()
            ->route('bimtek.tugas.index', $bimtek)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    /**
     * Display the specified tugas with submissions.
     */
    public function show(Bimtek $bimtek, Tugas $tugas): View
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);
        $user = Auth::user();

        // Load pengumpulan with user info
        $tugas->load(['pengumpulanTugas.user', 'pengumpulanTugas.penilai']);

        // Get user's submission if peserta
        $userSubmission = null;
        if ($isPeserta) {
            $userSubmission = $tugas->pengumpulanTugas->where('user_id', $user->id)->first();
        }

        // Get all peserta for this bimtek (for checking who hasn't submitted)
        $peserta = $bimtek->peserta;

        return view('tugas.show', compact('bimtek', 'tugas', 'canManage', 'isPeserta', 'userSubmission', 'peserta'));
    }

    /**
     * Show the form for editing the specified tugas.
     */
    public function edit(Bimtek $bimtek, Tugas $tugas): View
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        return view('tugas.edit', compact('bimtek', 'tugas'));
    }

    /**
     * Update the specified tugas.
     */
    public function update(Request $request, Bimtek $bimtek, Tugas $tugas): RedirectResponse
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'file_instruksi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:20480',
        ], [
            'judul.required' => 'Judul tugas wajib diisi.',
            'deadline.required' => 'Deadline wajib diisi.',
            'file_instruksi.mimes' => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'file_instruksi.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // Update file if new file uploaded
        if ($request->hasFile('file_instruksi')) {
            // Delete old file
            if ($tugas->file_instruksi_path) {
                Storage::disk('public')->delete($tugas->file_instruksi_path);
            }
            $validated['file_instruksi_path'] = $request->file('file_instruksi')->store("tugas/bimtek-{$bimtek->id}", 'public');
        }

        unset($validated['file_instruksi']);
        $tugas->update($validated);

        return redirect()
            ->route('bimtek.tugas.show', [$bimtek, $tugas])
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Remove the specified tugas.
     */
    public function destroy(Bimtek $bimtek, Tugas $tugas): RedirectResponse
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        // Delete file instruksi
        if ($tugas->file_instruksi_path) {
            Storage::disk('public')->delete($tugas->file_instruksi_path);
        }

        // Delete all pengumpulan files
        foreach ($tugas->pengumpulanTugas as $pengumpulan) {
            if ($pengumpulan->file_jawaban_path) {
                Storage::disk('public')->delete($pengumpulan->file_jawaban_path);
            }
        }

        $tugas->delete();

        return redirect()
            ->route('bimtek.tugas.index', $bimtek)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Download file instruksi tugas.
     */
    public function downloadInstruksi(Bimtek $bimtek, Tugas $tugas)
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        if (! $tugas->file_instruksi_path || ! Storage::disk('public')->exists($tugas->file_instruksi_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = pathinfo($tugas->file_instruksi_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($tugas->file_instruksi_path, $tugas->judul.'_instruksi.'.$ext);
    }

    /**
     * Preview file instruksi tugas.
     */
    public function previewInstruksi(Bimtek $bimtek, Tugas $tugas)
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        if (! $tugas->file_instruksi_path || ! Storage::disk('public')->exists($tugas->file_instruksi_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = strtolower(pathinfo($tugas->file_instruksi_path, PATHINFO_EXTENSION));
        $fileUrl = Storage::disk('public')->url($tugas->file_instruksi_path);
        $fullUrl = url($fileUrl);

        // PDF - langsung buka di browser
        if ($ext === 'pdf') {
            return response()->file(Storage::disk('public')->path($tugas->file_instruksi_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$tugas->judul.'_instruksi.pdf"',
            ]);
        }

        // Office files - gunakan Google Docs Viewer
        if (in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
            $googleViewerUrl = 'https://docs.google.com/viewer?url='.urlencode($fullUrl).'&embedded=true';

            return redirect()->away($googleViewerUrl);
        }

        // Untuk file lain - langsung download
        return $this->downloadInstruksi($bimtek, $tugas);
    }

    /**
     * Submit tugas (peserta).
     */
    public function submit(Request $request, Bimtek $bimtek, Tugas $tugas): RedirectResponse
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        // Only peserta can submit
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Hanya peserta yang dapat mengumpulkan tugas.');
        }

        if ($bimtek->status !== 'berlangsung') {
            return back()->with('error', 'Pengumpulan tugas hanya dapat dilakukan saat bimtek sedang berlangsung.');
        }

        // Check if already submitted
        $user = Auth::user();
        $existingSubmission = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        // Check deadline
        if ($tugas->isDeadlinePassed()) {
            return back()->with('error', 'Deadline sudah terlewat. Anda tidak dapat mengumpulkan tugas.');
        }

        $validated = $request->validate([
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:20480',
        ], [
            'file_jawaban.required' => 'File jawaban wajib diupload.',
            'file_jawaban.mimes' => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'file_jawaban.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // Upload file
        $filePath = $request->file('file_jawaban')->store("pengumpulan/bimtek-{$bimtek->id}/tugas-{$tugas->id}", 'public');

        // MENGGUNAKAN QUERY BUILDER AGAR AMAN DENGAN COMPOSITE PK
        DB::table('pengumpulan_tugas')->updateOrInsert(
            [
                'tugas_id' => $tugas->id,
                'user_id'  => $user->id,
            ],
            [
                'bimtek_id'         => $bimtek->id,
                'file_jawaban_path' => $filePath,
                'updated_at'        => now(),
                'created_at'        => now(),
            ]
        );

        return redirect()
            ->route('bimtek.tugas.show', [$bimtek, $tugas])
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Download pengumpulan jawaban.
     */
    public function downloadJawaban(Bimtek $bimtek, Tugas $tugas, User $user)
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentUser = Auth::id();
        $canView = $this->canManage($bimtek) || $pengumpulan->user_id === $currentUser;

        if (! $canView) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        if (! $pengumpulan->file_jawaban_path || ! Storage::disk('public')->exists($pengumpulan->file_jawaban_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = pathinfo($pengumpulan->file_jawaban_path, PATHINFO_EXTENSION);
        $filename = $tugas->judul.'_'.$user->name.'.'.$ext;

        return Storage::disk('public')->download($pengumpulan->file_jawaban_path, $filename);
    }

    /**
     * Preview pengumpulan jawaban.
     */
    public function previewJawaban(Bimtek $bimtek, Tugas $tugas, User $user)
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeAccess($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentUser = Auth::id();
        $canView = $this->canManage($bimtek) || $pengumpulan->user_id === $currentUser;

        if (! $canView) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        if (! $pengumpulan->file_jawaban_path || ! Storage::disk('public')->exists($pengumpulan->file_jawaban_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = strtolower(pathinfo($pengumpulan->file_jawaban_path, PATHINFO_EXTENSION));
        $fileUrl = Storage::disk('public')->url($pengumpulan->file_jawaban_path);
        $fullUrl = url($fileUrl);

        // PDF - langsung buka di browser
        if ($ext === 'pdf') {
            return response()->file(Storage::disk('public')->path($pengumpulan->file_jawaban_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="jawaban.pdf"',
            ]);
        }

        // Office files - gunakan Google Docs Viewer
        if (in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
            $googleViewerUrl = 'https://docs.google.com/viewer?url='.urlencode($fullUrl).'&embedded=true';

            return redirect()->away($googleViewerUrl);
        }

        // Untuk file lain - langsung download
        return $this->downloadJawaban($bimtek, $tugas, $user);
    }

    /**
     * Grade a submission (PIC/Panitia only).
     */
    public function grade(Request $request, Bimtek $bimtek, Tugas $tugas, User $user): RedirectResponse
    {
        $this->ensureHasTugas($bimtek);
        $this->authorizeManage($bimtek);
        $this->ensureTugasOwnership($bimtek, $tugas);

        // Pastikan data pengumpulan benar-benar ada
        $exists = DB::table('pengumpulan_tugas')
            ->where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            abort(404);
        }

        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ], [
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.integer' => 'Nilai harus berupa angka.',
            'nilai.min' => 'Nilai minimal 0.',
            'nilai.max' => 'Nilai maksimal 100.',
            'feedback.max' => 'Catatan penilaian maksimal 1000 karakter.',
        ]);

        // Update menggunakan Query Builder untuk tabel ber-composite key
        DB::table('pengumpulan_tugas')
            ->where('tugas_id', $tugas->id)
            ->where('user_id', $user->id)
            ->update([
                'nilai' => $validated['nilai'],
                'feedback' => $validated['feedback'],
                'user_id_penilai' => Auth::id(),
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('bimtek.tugas.show', [$bimtek, $tugas])
            ->with('success', 'Nilai final berhasil disimpan.');
    }

    /**
     * Check if user has access to view tugas.
     */
    protected function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Admin IT, Kepala, PPK can see all
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        // Check if user is involved in bimtek (PIC, Panitia, atau Peserta)
        $isInvolved = ($bimtek->pic_user_id === $user->id) 
            || $bimtek->panitia()->where('user_id', $user->id)->exists() 
            || $bimtek->peserta()->where('user_id', $user->id)->exists();

        // Check if user is the pengajuan owner
        $isOwner = $bimtek->pengajuan && $bimtek->pengajuan->user_id === $user->id;

        if (! $isInvolved && ! $isOwner) {
            abort(403, 'Anda tidak memiliki akses ke tugas bimtek ini.');
        }
    }

    /**
     * Check if user can manage (create/edit/delete/grade) tugas.
     */
    protected function authorizeManage(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Only PIC or Panitia can manage tugas
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola tugas.');
        }
    }

    /**
     * Check if user can manage tugas (for view).
     */
    protected function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();

        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        return $isPic || $isPanitia;
    }

    /**
     * Check if current user is peserta of this bimtek.
     */
    protected function isPeserta(Bimtek $bimtek): bool
    {
        $user = Auth::user();

        return $bimtek->peserta()->where('users.id', $user->id)->exists();
    }

    /**
     * Ensure tugas belongs to bimtek.
     */
    protected function ensureTugasOwnership(Bimtek $bimtek, Tugas $tugas): void
    {
        if ($tugas->bimtek_id !== $bimtek->id) {
            abort(404, 'Tugas tidak ditemukan.');
        }
    }

    /**
     * Abort if bimtek has tugas disabled.
     */
    protected function ensureHasTugas(Bimtek $bimtek): void
    {
        if (! $bimtek->has_tugas) {
            abort(404, 'Fitur Tugas dinonaktifkan untuk bimtek ini.');
        }
    }
}