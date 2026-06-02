<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RtController extends Controller
{
    /**
     * Display list of pengajuan yang sudah disetujui final untuk RT.
     */
    public function index(Request $request): View
    {
        $query = Pengajuan::with(['user', 'fasilitasLogistiks'])
            ->where('status_pengajuan', 'disetujui_final')
            ->whereHas('fasilitasLogistiks'); // Hanya yang punya fasilitas

        // Filter berdasarkan status RT
        if ($request->filled('status')) {
            $query->where('status_rt', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                    ->orWhere('tempat_kegiatan', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'belum_dipenuhi' => 'Belum Dipenuhi',
            'sebagian_dipenuhi' => 'Sebagian Dipenuhi',
            'telah_dipenuhi' => 'Telah Dipenuhi',
        ];

        // Statistics with single query for performance
        $baseQuery = Pengajuan::where('status_pengajuan', 'disetujui_final')
            ->whereHas('fasilitasLogistiks');

        $stats = $baseQuery->selectRaw("
            SUM(CASE WHEN status_rt = 'belum_dipenuhi' THEN 1 ELSE 0 END) as belum_dipenuhi,
            SUM(CASE WHEN status_rt = 'sebagian_dipenuhi' THEN 1 ELSE 0 END) as sebagian_dipenuhi,
            SUM(CASE WHEN status_rt = 'telah_dipenuhi' THEN 1 ELSE 0 END) as telah_dipenuhi
        ")->first();

        return view('rt.index', compact('pengajuans', 'statusOptions', 'stats'));
    }

    /**
     * Show detail pengajuan untuk RT.
     */
    public function show(Pengajuan $pengajuan): View
    {
        if ($pengajuan->status_pengajuan !== 'disetujui_final') {
            abort(403, 'Pengajuan belum disetujui final.');
        }

        $pengajuan->load(['user', 'fasilitasLogistiks']);

        return view('rt.show', compact('pengajuan'));
    }

    /**
     * Update status pemenuhan fasilitas.
     */
    public function update(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        if ($pengajuan->status_pengajuan !== 'disetujui_final') {
            return back()->with('error', 'Pengajuan belum disetujui final.');
        }

        $request->validate([
            'fasilitas_dipenuhi' => 'nullable|array',
            'fasilitas_dipenuhi.*' => 'exists:fasilitas_logistiks,id',
            'catatan_rt' => 'nullable|string|max:1000',
        ]);

        $fasilitasDipenuhi = $request->fasilitas_dipenuhi ?? [];

        // Update status setiap fasilitas
        foreach ($pengajuan->fasilitasLogistiks as $fasilitas) {
            $isDipenuhi = in_array($fasilitas->id, $fasilitasDipenuhi);
            $fasilitas->update([
                'is_dipenuhi' => $isDipenuhi,
                'status' => $isDipenuhi ? 'tersedia' : 'diminta',
            ]);
        }

        // Hitung status RT berdasarkan pemenuhan fasilitas
        $totalFasilitas = $pengajuan->fasilitasLogistiks->count();
        $totalDipenuhi = count($fasilitasDipenuhi);

        if ($totalDipenuhi === 0) {
            $statusRt = 'belum_dipenuhi';
        } elseif ($totalDipenuhi === $totalFasilitas) {
            $statusRt = 'telah_dipenuhi';
        } else {
            $statusRt = 'sebagian_dipenuhi';
        }

        // Update pengajuan
        $pengajuan->update([
            'status_rt' => $statusRt,
            'catatan_rt' => $request->catatan_rt,
        ]);

        return redirect()
            ->route('rt.show', $pengajuan)
            ->with('success', 'Status pemenuhan fasilitas berhasil diperbarui.');
    }
}
