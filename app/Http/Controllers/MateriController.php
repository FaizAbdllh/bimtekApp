<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MateriController extends Controller
{
    /**
     * Display a listing of materi for a bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);

        $bimtek->load(['materis' => function ($q) {
            $q->latest();
        }]);

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);

        // Check verification status for peserta
        $isVerified = false;
        if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
            $user = Auth::user();
            $pivot = $bimtek->users()
                ->where('users.id', $user->id)
                ->where('bimtek_user.peran_kontekstual', 'peserta')
                ->first();
            $statusVerifikasi = $pivot?->pivot->status_verifikasi ?? 'invited';
            $isVerified = $statusVerifikasi === 'verified';
        } else {
            $isVerified = true; // Non-peserta or no verification required
        }

        return view('materi.index', compact('bimtek', 'canManage', 'isVerified'));
    }

    /**
     * Show the form for creating a new materi.
     */
    public function create(Bimtek $bimtek): View
    {
        $this->authorizeManage($bimtek);

        $tipeOptions = [
            'materi' => 'Materi Pembelajaran',
            'panduan' => 'Panduan/Petunjuk',
        ];

        return view('materi.create', compact('bimtek', 'tipeOptions'));
    }

    /**
     * Store a newly created materi.
     */
    public function store(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:materi,panduan',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:20480',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'tipe.required' => 'Tipe materi wajib dipilih.',
            'file.required' => 'File materi wajib diupload.',
            'file.mimes' => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'file.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // Upload file
        $path = $request->file('file')->store("materi/bimtek-{$bimtek->id}", 'public');

        Materi::create([
            'bimtek_id' => $bimtek->id,
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'file_path' => $path,
        ]);

        return redirect()
            ->route('bimtek.materi.index', $bimtek)
            ->with('success', 'Materi berhasil diupload.');
    }

    /**
     * Show the form for editing the specified materi.
     */
    public function edit(Bimtek $bimtek, Materi $materi): View
    {
        $this->authorizeManage($bimtek);
        $this->ensureMateriOwnership($bimtek, $materi);

        $tipeOptions = [
            'materi' => 'Materi Pembelajaran',
            'panduan' => 'Panduan/Petunjuk',
        ];

        return view('materi.edit', compact('bimtek', 'materi', 'tipeOptions'));
    }

    /**
     * Update the specified materi.
     */
    public function update(Request $request, Bimtek $bimtek, Materi $materi): RedirectResponse
    {
        $this->authorizeManage($bimtek);
        $this->ensureMateriOwnership($bimtek, $materi);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:materi,panduan',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:20480',
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'tipe.required' => 'Tipe materi wajib dipilih.',
            'file.mimes' => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'file.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // Update file if new file uploaded
        if ($request->hasFile('file')) {
            // Delete old file
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $validated['file_path'] = $request->file('file')->store("materi/bimtek-{$bimtek->id}", 'public');
        }

        unset($validated['file']);
        $materi->update($validated);

        return redirect()
            ->route('bimtek.materi.index', $bimtek)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the specified materi.
     */
    public function destroy(Bimtek $bimtek, Materi $materi): RedirectResponse
    {
        $this->authorizeManage($bimtek);
        $this->ensureMateriOwnership($bimtek, $materi);

        // Delete file
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return redirect()
            ->route('bimtek.materi.index', $bimtek)
            ->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Download materi file.
     */
    public function download(Bimtek $bimtek, Materi $materi)
    {
        $this->authorizeAccess($bimtek);
        $this->ensureMateriOwnership($bimtek, $materi);

        if (! $materi->file_path || ! Storage::disk('public')->exists($materi->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($materi->file_path, $materi->judul.'.'.pathinfo($materi->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Preview materi file (inline view or external viewer).
     */
    public function preview(Bimtek $bimtek, Materi $materi)
    {
        $this->authorizeAccess($bimtek);
        $this->ensureMateriOwnership($bimtek, $materi);

        if (! $materi->file_path || ! Storage::disk('public')->exists($materi->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
        $fileUrl = Storage::disk('public')->url($materi->file_path);
        $fullUrl = url($fileUrl);

        // PDF - langsung buka di browser
        if ($ext === 'pdf') {
            return response()->file(Storage::disk('public')->path($materi->file_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$materi->judul.'.pdf"',
            ]);
        }

        // Office files (DOC, DOCX, PPT, PPTX, XLS, XLSX) - gunakan Google Docs Viewer
        if (in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
            $googleViewerUrl = 'https://docs.google.com/viewer?url='.urlencode($fullUrl).'&embedded=true';

            return redirect()->away($googleViewerUrl);
        }

        // Untuk file lain (ZIP, RAR) - langsung download
        return $this->download($bimtek, $materi);
    }

    /**
     * Check if user has access to view materi.
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
            abort(403, 'Anda tidak memiliki akses ke materi bimtek ini.');
        }
    }

    /**
     * Check if user can manage (upload/edit/delete) materi.
     */
    protected function authorizeManage(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Only PIC or Panitia can manage materi
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola materi.');
        }
    }

    /**
     * Check if user can manage materi (for view).
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
        return $bimtek->peserta()->where('users.id', Auth::id())->exists();
    }

    /**
     * Ensure materi belongs to bimtek.
     */
    protected function ensureMateriOwnership(Bimtek $bimtek, Materi $materi): void
    {
        if ($materi->bimtek_id !== $bimtek->id) {
            abort(404, 'Materi tidak ditemukan.');
        }
    }
}
