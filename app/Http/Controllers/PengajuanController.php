<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Http\Requests\UpdatePengajuanRequest;
use App\Models\Pengajuan;
use App\Models\SbmMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Pengajuan::with(['user']);

        // Jika Pegawai Internal, hanya tampilkan pengajuan miliknya
        if ($user->isPegawaiInternal()) {
            $query->where('user_id', $user->id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        // Filter berdasarkan jenis kegiatan
        if ($request->filled('jenis')) {
            $query->where('jenis_kegiatan', $request->jenis);
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

        // Status options untuk filter
        $statusOptions = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disetujui_kepala' => 'Disetujui Kepala',
            'disetujui_ppk' => 'Disetujui PPK',
            'disetujui_final' => 'Disetujui Final',
            'ditolak' => 'Ditolak',
            'perlu_revisi' => 'Perlu Revisi',
        ];

        return view('pengajuan.index', compact('pengajuans', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $sbmMasters = SbmMaster::active()
            ->tahunBerlaku(2026)
            ->orderBy('kategori')
            ->orderBy('nama_item')
            ->get()
            ->groupBy('kategori');

        return view('pengajuan.create', compact('sbmMasters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $validated['user_id'] = Auth::id();

            // Cek apakah ini draft atau submit
            $isDraft = $request->has('save_draft');
            $validated['is_draft'] = $isDraft;
            $validated['status_pengajuan'] = $isDraft ? 'draft' : 'diajukan';
            $validated['mode_pelaksanaan'] = $validated['mode_pelaksanaan'] ?? 'offline';

            $pengajuan = Pengajuan::create($validated);

            // Simpan rancangan anggaran biaya (RAB)
            if ($request->has('anggaran') && is_array($request->anggaran)) {
                foreach ($request->anggaran as $item) {
                    // Skip jika nama dan harga kosong
                    if (empty($item['nama_item']) && ! $item['total_biaya']) {
                        continue;
                    }

                    $pengajuan->kebutuhanAnggarans()->create([
                        'sbm_master_id' => $item['sbm_master_id'] ?? null,
                        'nama_item' => $item['nama_item'] ?? '',
                        'kategori' => $item['kategori'] ?? 'lainnya',
                        'volume_1' => $item['volume_1'] ?? 0,
                        'satuan_1' => $item['satuan_primary'] ?? '',
                        'volume_2' => $item['volume_2'] ?? 0,
                        'satuan_2' => $item['satuan_secondary'] ?? '',
                        'harga_satuan' => $item['harga_satuan'] ?? 0,
                        'harga_satuan_sbm' => $item['harga_satuan_sbm'] ?? 0,
                        'total_biaya' => $item['total_biaya'] ?? 0,
                    ]);
                }
            }

            // Simpan fasilitas logistik
            if ($request->has('fasilitas') && is_array($request->fasilitas)) {
                foreach ($request->fasilitas as $item) {
                    // Skip jika nama kosong
                    if (empty($item['nama'])) {
                        continue;
                    }

                    $pengajuan->fasilitasLogistiks()->create([
                        'nama_fasilitas' => $item['nama'],
                        'jumlah' => $item['jumlah'] ?? 1,
                        'satuan' => $item['satuan'] ?? 'Unit',
                        'status' => 'diminta',
                    ]);
                }
            }

            DB::commit();

            if ($isDraft) {
                return redirect()
                    ->route('pengajuan.edit', $pengajuan)
                    ->with('success', 'Draft pengajuan berhasil disimpan.');
            }

            return redirect()
                ->route('pengajuan.show', $pengajuan)
                ->with('success', 'Pengajuan bimtek berhasil dibuat dan menunggu persetujuan Kepala.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengajuan $pengajuan): View
    {
        $user = Auth::user();

        // Load role relation untuk pengecekan akses
        $user->load('role');

        // Cek akses: pemilik, Admin IT, Kepala, PPK, atau Koordinator RT bisa melihat
        $canAccess = $pengajuan->user_id === $user->id
            || $user->isAdminIt()
            || $user->isKepala()
            || $user->isPpk()
            || $user->isRt();

        if (! $canAccess) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan ini.');
        }

        $pengajuan->load(['user', 'fasilitasLogistiks', 'bimtek']);

        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengajuan $pengajuan): View|RedirectResponse
    {
        $user = Auth::user();

        // Pemilik atau Admin IT bisa edit
        if ($pengajuan->user_id !== $user->id && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        if (! in_array($pengajuan->status_pengajuan, ['draft', 'diajukan', 'perlu_revisi'])) {
            return redirect()
                ->route('pengajuan.show', $pengajuan)
                ->with('error', 'Pengajuan tidak dapat diedit karena sudah diproses.');
        }

        $sbmMasters = SbmMaster::active()
            ->tahunBerlaku(2026)
            ->orderBy('kategori')
            ->orderBy('nama_item')
            ->get()
            ->groupBy('kategori');

        return view('pengajuan.edit', compact('pengajuan', 'sbmMasters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        $user = Auth::user();

        // Pemilik atau Admin IT bisa update
        if ($pengajuan->user_id !== $user->id && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate pengajuan ini.');
        }

        if (! in_array($pengajuan->status_pengajuan, ['draft', 'diajukan', 'perlu_revisi'])) {
            return redirect()
                ->route('pengajuan.show', $pengajuan)
                ->with('error', 'Pengajuan tidak dapat diupdate karena sudah diproses.');
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Cek apakah ini simpan draft atau submit
            $isDraft = $request->has('save_draft');
            $validated['is_draft'] = $isDraft;

            // Update status berdasarkan aksi
            if ($isDraft) {
                $validated['status_pengajuan'] = 'draft';
            } elseif ($pengajuan->status_pengajuan === 'perlu_revisi') {
                // Jika revisi dari PPK (Kepala sudah approve sebelumnya), langsung ke disetujui_kepala
                // Jika revisi dari Kepala, kembali ke diajukan
                if ($pengajuan->kepala_approved_at) {
                    $validated['status_pengajuan'] = 'disetujui_kepala';
                } else {
                    $validated['status_pengajuan'] = 'diajukan';
                }
            } elseif ($pengajuan->status_pengajuan === 'draft') {
                $validated['status_pengajuan'] = 'diajukan';
            }

            $validated['mode_pelaksanaan'] = $validated['mode_pelaksanaan'] ?? 'offline';

            $pengajuan->update($validated);

            // Update rancangan anggaran biaya (RAB) - hapus yang lama dan simpan yang baru
            if ($request->has('anggaran')) {
                $pengajuan->kebutuhanAnggarans()->delete();

                foreach ($request->anggaran as $item) {
                    // Skip jika nama dan harga kosong
                    if (empty($item['nama_item']) && ! $item['total_biaya']) {
                        continue;
                    }

                    $pengajuan->kebutuhanAnggarans()->create([
                        'sbm_master_id' => $item['sbm_master_id'] ?? null,
                        'nama_item' => $item['nama_item'] ?? '',
                        'kategori' => $item['kategori'] ?? 'lainnya',
                        'volume_1' => $item['volume_1'] ?? 0,
                        'satuan_1' => $item['satuan_primary'] ?? '',
                        'volume_2' => $item['volume_2'] ?? 0,
                        'satuan_2' => $item['satuan_secondary'] ?? '',
                        'harga_satuan' => $item['harga_satuan'] ?? 0,
                        'harga_satuan_sbm' => $item['harga_satuan_sbm'] ?? 0,
                        'total_biaya' => $item['total_biaya'] ?? 0,
                    ]);
                }
            }

            // Update fasilitas logistik - hapus yang lama dan simpan yang baru
            if ($request->has('fasilitas')) {
                $pengajuan->fasilitasLogistiks()->delete();

                foreach ($request->fasilitas as $item) {
                    // Skip jika nama kosong
                    if (empty($item['nama'])) {
                        continue;
                    }

                    $pengajuan->fasilitasLogistiks()->create([
                        'nama_fasilitas' => $item['nama'],
                        'jumlah' => $item['jumlah'] ?? 1,
                        'satuan' => $item['satuan'] ?? 'Unit',
                        'status' => 'diminta',
                    ]);
                }
            }

            DB::commit();

            if ($isDraft) {
                return redirect()
                    ->route('pengajuan.edit', $pengajuan)
                    ->with('success', 'Draft pengajuan berhasil disimpan.');
            }

            return redirect()
                ->route('pengajuan.show', $pengajuan)
                ->with('success', 'Pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pengajuan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengajuan $pengajuan): RedirectResponse
    {
        $user = Auth::user();

        // Hanya pemilik atau Admin IT yang bisa hapus
        if (! $user->isAdminIt() && $pengajuan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        // Tidak bisa hapus jika sudah disetujui final atau sudah ada bimtek
        if ($pengajuan->status_pengajuan === 'disetujui_final' || $pengajuan->bimtek) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Pengajuan tidak dapat dihapus karena sudah diproses menjadi Bimtek.');
        }

        $pengajuan->delete();

        return redirect()
            ->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
