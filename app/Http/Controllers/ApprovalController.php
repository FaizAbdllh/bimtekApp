<?php

namespace App\Http\Controllers;

use App\Models\Bimtek; // <-- Menggunakan model Bimtek sebagai single-source-of-truth
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Tampilkan daftar usulan kegiatan untuk persetujuan Kepala Balai.
     */
    public function indexKepala(Request $request): View
    {
        // Mengubah query ke model Bimtek dan mengubah relasi user menjadi pic
        $query = Bimtek::with(['pic'])
            ->whereIn('status', ['diajukan', 'disetujui_kepala', 'ditolak', 'perlu_revisi']);

        // Filter berdasarkan status alur kerja
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fitur pencarian usulan kegiatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                    ->orWhereHas('pic', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Variabel tetap bernama $pengajuans agar halaman View blade Kepala tidak rusak
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
     * Tampilkan detail usulan kegiatan untuk Kepala Balai.
     */
    public function showKepala(Bimtek $pengajuan): View
    {
        $pengajuan->load(['pic', 'fasilitasLogistiks']);

        return view('approval.kepala.show', compact('pengajuan'));
    }

    /**
     * Aksi Persetujuan Usulan Kegiatan oleh Kepala Balai.
     */
    public function approveKepala(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT hanya memiliki hak read-only. Tidak dapat melakukan approval.');
        }

        if ($pengajuan->status !== 'diajukan') {
            return back()->with('error', 'Usulan kegiatan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Menaikkan state status alur kegiatan ke tahap berikutnya
        $pengajuan->update([
            'status' => 'disetujui_kepala',
            'catatan_kepala' => $request->catatan,
            'kepala_approved_at' => now(),
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Pengajuan berhasil disetujui dan diteruskan ke PPK untuk review anggaran.');
    }

    /**
     * Aksi Penolakan Usulan Kegiatan oleh Kepala Balai.
     */
    public function rejectKepala(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Admin IT tidak memiliki wewenang birokrasi.');
        }

        if ($pengajuan->status !== 'diajukan') {
            return back()->with('error', 'Usulan kegiatan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi sebagai transparansi alasan penolakan.',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_kepala' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Usulan kegiatan resmi ditolak.');
    }

    /**
     * Aksi Pengembalian Usulan untuk Revisi oleh Kepala Balai.
     */
    public function revisiKepala(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        if ($pengajuan->status !== 'diajukan') {
            return back()->with('error', 'Usulan kegiatan ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan arahan revisi wajib diisi agar pengaju tahu apa yang harus diperbaiki.',
        ]);

        $pengajuan->update([
            'status' => 'perlu_revisi',
            'catatan_kepala' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.kepala.index')
            ->with('success', 'Usulan kegiatan dikembalikan kepada pengaju untuk direvisi.');
    }

    /**
     * Tampilkan daftar review anggaran untuk persetujuan PPK.
     */
    public function indexPpk(Request $request): View
    {
        $query = Bimtek::with(['pic'])
            ->whereIn('status', ['disetujui_kepala', 'disetujui_ppk', 'disetujui_final']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                    ->orWhereHas('pic', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'disetujui_kepala' => 'Menunggu Persetujuan Anggaran',
            'disetujui_ppk' => 'Disetujui PPK',
            'disetujui_final' => 'Disetujui Final',
        ];

        return view('approval.ppk.index', compact('pengajuans', 'statusOptions'));
    }

    /**
     * Tampilkan detail pagu anggaran SBM untuk PPK.
     */
    public function showPpk(Bimtek $pengajuan): View
    {
        $pengajuan->load(['pic', 'fasilitasLogistiks', 'kebutuhanAnggarans.sbmMaster']);

        return view('approval.ppk.show', compact('pengajuan'));
    }

    /**
     * Aksi Persetujuan Pagu Anggaran Akhir oleh PPK (Final Approval).
     */
    public function approvePpk(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        if ($pengajuan->status !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu review anggaran PPK.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        // REFAKTORISASI RADIKAL: Cukup update status baris data yang sama ke disetujui_final.
        // Tidak ada proses penyuntikan row baru (Bimtek::create) karena datanya sudah menyatu!
        $pengajuan->update([
            'status' => 'disetujui_final',
            'catatan_ppk' => $request->catatan,
            'ppk_approved_at' => now(),
            
            // Mengunci data perencanaan awal ke data aktual pelaksanaan sebagai inisialisasi awal kelas
            'judul_final' => $pengajuan->judul_rencana,
            'lokasi_aktual' => $pengajuan->tempat_kegiatan_rencana,
            'tanggal_mulai_aktual' => $pengajuan->tanggal_mulai_rencana,
            'tanggal_selesai_aktual' => $pengajuan->tanggal_selesai_rencana,
        ]);

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Usulan kegiatan telah disetujui final oleh PPK dan siap memasuki tahap Persiapan Kelompok Kerja.');
    }

    /**
     * Aksi Penolakan Anggaran oleh PPK.
     */
    public function rejectPpk(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        if ($pengajuan->status !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu review anggaran PPK.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Alasan penolakan anggaran wajib dicantumkan.',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Usulan anggaran kegiatan resmi ditolak oleh PPK.');
    }

    /**
     * Aksi Pengembalian Anggaran untuk Revisi RAB oleh PPK.
     */
    public function revisiPpk(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (auth()->user()->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        if ($pengajuan->status !== 'disetujui_kepala') {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu review anggaran PPK.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan koreksi kelayakan harga SBM wajib diisi.',
        ]);

        $pengajuan->update([
            'status' => 'perlu_revisi',
            'catatan_ppk' => $request->catatan,
        ]);

        return redirect()
            ->route('approval.ppk.index')
            ->with('success', 'Berkas dikembalikan ke pengaju untuk revisi penyesuaian SBM.');
    }
}