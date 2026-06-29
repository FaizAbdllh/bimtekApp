<?php

namespace App\Http\Controllers;

use App\Models\Bimtek; // <-- Mengubah impor dari Pengajuan ke model Bimtek
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RtController extends Controller
{
    /**
     * Tampilkan daftar usulan kegiatan yang membutuhkan fasilitas logistik Rumah Tangga.
     */
    public function index(Request $request): View
    {
        // Menggunakan model Bimtek, mengubah status_pengajuan menjadi status, dan user menjadi pic
        $query = Bimtek::with(['pic', 'fasilitasLogistiks'])
            ->whereIn('status', ['disetujui_final', 'persiapan', 'berlangsung', 'selesai']) // RT tetap bisa melihat data meskipun bimtek sudah berjalan
            ->whereHas('fasilitasLogistiks'); // Hanya tampilkan kegiatan yang meminta fasilitas logistik

        // Filter berdasarkan status pemenuhan RT
        if ($request->filled('status')) {
            $query->where('status_rt', $request->status);
        }

        // Pencarian berdasarkan judul kegiatan, tempat, atau nama PIC pengaju
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                    ->orWhere('judul_final', 'like', "%{$search}%")
                    ->orWhere('tempat_kegiatan_rencana', 'like', "%{$search}%") // Kolom disesuaikan dengan create_bimteks_table
                    ->orWhereHas('pic', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Variabel tetap bernama $pengajuans agar halaman View Blade RT kamu tidak crash
        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'belum_dipenuhi' => 'Belum Dipenuhi',
            'sebagian_dipenuhi' => 'Sebagian Dipenuhi',
            'telah_dipenuhi' => 'Telah Dipenuhi',
        ];

        // Optimasi query agregasi statistik Rumah Tangga langsung dari model Bimtek
        $baseQuery = Bimtek::whereIn('status', ['disetujui_final', 'persiapan', 'berlangsung', 'selesai'])
            ->whereHas('fasilitasLogistiks');

        $stats = $baseQuery->selectRaw("
            SUM(CASE WHEN status_rt = 'belum_dipenuhi' THEN 1 ELSE 0 END) as belum_dipenuhi,
            SUM(CASE WHEN status_rt = 'sebagian_dipenuhi' THEN 1 ELSE 0 END) as sebagian_dipenuhi,
            SUM(CASE WHEN status_rt = 'telah_dipenuhi' THEN 1 ELSE 0 END) as telah_dipenuhi
        ")->first();

        return view('rt.index', compact('pengajuans', 'statusOptions', 'stats'));
    }

    /**
     * Tampilkan detail permintaan fasilitas logistik untuk Rumah Tangga.
     */
    public function show(Bimtek $pengajuan): View
    {
        // Mengubah pengecekan status berdasarkan transisi state baru
        if (in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi', 'ditolak'])) {
            abort(403, 'Akses ditolak. Kegiatan ini belum disetujui final oleh Pejabat PPK.');
        }

        $pengajuan->load(['pic', 'fasilitasLogistiks']);

        return view('rt.show', compact('pengajuan'));
    }

    /**
     * Update status pemenuhan checklist fasilitas logistik.
     */
    public function update(Request $request, Bimtek $pengajuan): RedirectResponse
    {
        if (in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi', 'ditolak'])) {
            return back()->with('error', 'Gagal memproses. Kegiatan belum disetujui final.');
        }

        $request->validate([
            'fasilitas_dipenuhi' => 'nullable|array',
            'fasilitas_dipenuhi.*' => 'exists:fasilitas_logistiks,id',
            'catatan_rt' => 'nullable|string|max:1000',
        ]);

        $fasilitasDipenuhi = $request->fasilitas_dipenuhi ?? [];

        // Update status centang pada setiap komponen barang logistik di tabel fasilitas_logistiks
        foreach ($pengajuan->fasilitasLogistiks as $fasilitas) {
            $isDipenuhi = in_array($fasilitas->id, $fasilitasDipenuhi);
            $fasilitas->update([
                'is_dipenuhi' => $isDipenuhi,
                'status' => $isDipenuhi ? 'tersedia' : 'diminta',
            ]);
        }

        // Kalkulasi otomatis penentuan status_rt berdasarkan rasio jumlah checklist barang
        $totalFasilitas = $pengajuan->fasilitasLogistiks->count();
        $totalDipenuhi = count($fasilitasDipenuhi);

        if ($totalDipenuhi === 0) {
            $statusRt = 'belum_dipenuhi';
        } elseif ($totalDipenuhi === $totalFasilitas) {
            $statusRt = 'telah_dipenuhi';
        } else {
            $statusRt = 'sebagian_dipenuhi';
        }

        // Kunci status akhir ke baris data induk kegiatan
        $pengajuan->update([
            'status_rt' => $statusRt,
            'catatan_rt' => $request->catatan_rt,
        ]);

        return redirect()
            ->route('rt.show', $pengajuan->id)
            ->with('success', 'Status pemenuhan sarana fasilitas logistik berhasil diperbarui.');
    }
}