<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Display list of pengajuan for Kepala approval.
     */
    public function indexKepala(Request $request): View
    {
        $query = Pengajuan::with(['user'])
            ->whereIn('status_pengajuan', ['diajukan', 'disetujui_kepala', 'ditolak', 'perlu_revisi']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'diajukan' => 'Menunggu Persetujuan',
            'disetujui_kepala' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'perlu_revisi' => 'Perlu Revisi',
        ];

        return view('approval.kepala.index', compact('pengajuans', 'statusOptions'));
    }

    /**
     * Show detail pengajuan for Kepala approval.
     */
    public function showKepala(Pengajuan $pengajuan): View
    {
        $pengajuan->load(['user', 'fasilitasLogistiks']);
        return view('approval.kepala.show', compact('pengajuan'));
    }

    /**
     * Approve pengajuan by Kepala.
     */
    public function approveKepala(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'diajukan') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $pengajuan->update([
            'status_pengajuan' => 'disetujui_kepala',
            'catatan_kepala' => $request->catatan,
            'kepala_approved_at' => now(),
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Pengajuan berhasil disetujui dan diteruskan ke PPK.');
    }

    /**
     * Reject pengajuan by Kepala.
     */
    public function rejectKepala(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'diajukan') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan wajib diisi saat menolak pengajuan.',
        ]);

        $pengajuan->update([
            'status_pengajuan' => 'ditolak',
            'catatan_kepala' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Pengajuan telah ditolak.');
    }

    /**
     * Request revision by Kepala.
     */
    public function revisiKepala(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'diajukan') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi.',
        ]);

        $pengajuan->update([
            'status_pengajuan' => 'perlu_revisi',
            'catatan_kepala' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Pengajuan dikembalikan untuk revisi.');
    }

    /**
     * Display list of pengajuan for PPK approval.
     */
    public function indexPpk(Request $request): View
    {
        $query = Pengajuan::with(['user'])
            ->whereIn('status_pengajuan', ['disetujui_kepala', 'disetujui_ppk', 'disetujui_final']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'disetujui_kepala' => 'Menunggu Persetujuan',
            'disetujui_ppk' => 'Disetujui PPK',
            'disetujui_final' => 'Disetujui Final',
        ];

        return view('approval.ppk.index', compact('pengajuans', 'statusOptions'));
    }

    /**
     * Show detail pengajuan for PPK approval.
     */
    public function showPpk(Pengajuan $pengajuan): View
    {
        $pengajuan->load(['user', 'fasilitasLogistiks']);
        return view('approval.ppk.show', compact('pengajuan'));
    }

    /**
     * Approve pengajuan by PPK (final approval - creates Bimtek).
     */
    public function approvePpk(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan PPK.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Update status pengajuan
        $pengajuan->update([
            'status_pengajuan' => 'disetujui_final',
            'catatan_ppk' => $request->catatan,
        ]);

        $bimtekPayload = [
            'pengajuan_id' => $pengajuan->id,
            'pic_user_id' => $pengajuan->user_id, // Pengaju otomatis jadi PIC
            'judul_final' => $pengajuan->judul_rencana,
            'mode_pelaksanaan' => $pengajuan->mode_pelaksanaan ?? 'offline',
            'lokasi_aktual' => $pengajuan->tempat_kegiatan,
            'tanggal_mulai_aktual' => $pengajuan->tanggal_mulai_rencana,
            'tanggal_selesai_aktual' => $pengajuan->tanggal_selesai_rencana,
            // Inherit verifikasi dokumen fields from pengajuan
            'butuh_verifikasi_dokumen' => $pengajuan->butuh_verifikasi_dokumen,
            'jenis_dokumen_wajib' => $pengajuan->jenis_dokumen_wajib,
        ];

        if ($pengajuan->bimtek) {
            $pengajuan->bimtek->update($bimtekPayload);
        } else {
            $bimtekPayload['status_pelaksanaan'] = 'persiapan';
            Bimtek::create($bimtekPayload);
        }

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Pengajuan disetujui final. Bimtek telah dibuat dan pengaju ditetapkan sebagai PIC.');
    }

    /**
     * Reject pengajuan by PPK (return to Kepala).
     */
    public function rejectPpk(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan PPK.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan wajib diisi saat menolak pengajuan.',
        ]);

        // Tolak pengajuan
        $pengajuan->update([
            'status_pengajuan' => 'ditolak',
            'catatan_ppk' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Pengajuan telah ditolak.');
    }

    /**
     * Request revision by PPK (return to PIC for revision).
     */
    public function revisiPpk(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Block Admin IT
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki akses untuk approval.');
        }

        if ($pengajuan->status_pengajuan !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu persetujuan PPK.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi.',
        ]);

        // Kembalikan ke PIC untuk revisi (status perlu_revisi)
        $pengajuan->update([
            'status_pengajuan' => 'perlu_revisi',
            'catatan_ppk' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Pengajuan dikembalikan ke pengaju untuk direvisi.');
    }
}
