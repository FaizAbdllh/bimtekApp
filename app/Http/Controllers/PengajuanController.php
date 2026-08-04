<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Http\Requests\UpdatePengajuanRequest;
use App\Models\Bimtek; 
use App\Models\SbmMaster;
use App\Models\SyaratDokumen;
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
        $query = Bimtek::with(['pic']);

        if ($user->isPegawaiInternal()) {
            $query->where('pic_user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kegiatan', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_rencana', 'like', "%{$search}%")
                    ->orWhere('tempat_kegiatan_rencana', 'like', "%{$search}%")
                    ->orWhereHas('pic', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $statusOptions = [
            'draft_pic' => 'Draft',
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
            
            $validated['pic_user_id'] = Auth::id();

            // Intersepsi status checkbox agar tidak terbuang
            $validated['butuh_verifikasi_dokumen'] = $request->has('butuh_verifikasi_dokumen') ? 1 : 0;

            $isDraft = $request->has('save_draft');
            $validated['status'] = $isDraft ? 'draft_pic' : 'diajukan';
            $validated['mode_pelaksanaan'] = $validated['mode_pelaksanaan'] ?? 'offline';

            $validated['judul_rencana'] = $validated['judul_rencana'] ?? $request->input('judul');
            $validated['tempat_kegiatan_rencana'] = $validated['tempat_kegiatan_rencana'] ?? $request->input('tempat_kegiatan');
            $validated['tanggal_mulai_rencana'] = $validated['tanggal_mulai_rencana'] ?? $request->input('tanggal_mulai');
            $validated['tanggal_selesai_rencana'] = $validated['tanggal_selesai_rencana'] ?? $request->input('tanggal_selesai');
            $validated['deskripsi_rencana'] = $validated['deskripsi_rencana'] ?? $request->input('deskripsi');

            $pengajuan = Bimtek::create($validated);

            // Proses simpan dokumen persyaratan
            if ($validated['butuh_verifikasi_dokumen'] && $request->filled('syarat_dokumen')) {
                foreach ($request->input('syarat_dokumen') as $doc) {
                    if (empty($doc)) continue;

                    $namaDokumen = is_array($doc) ? ($doc['nama_dokumen'] ?? '') : $doc;
                    $deskripsi   = is_array($doc) ? ($doc['deskripsi_syarat'] ?? null) : null;
                    $isWajib     = is_array($doc) ? (isset($doc['is_wajib']) ? 1 : 0) : 1;

                    if (!empty($namaDokumen)) {
                        SyaratDokumen::create([
                            'bimtek_id'        => $pengajuan->id,
                            'nama_dokumen'     => $namaDokumen,
                            'deskripsi_syarat' => $deskripsi,
                            'is_wajib'         => $isWajib,
                        ]);
                    }
                }
            }

            // Menyimpan akumulasi dana komponen anggaran belanja SBM
            if ($request->has('anggaran') && is_array($request->anggaran)) {
                foreach ($request->anggaran as $item) {
                    if (empty($item['nama_item']) && !($item['total_biaya'] ?? 0)) {
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

            // Menyimpan data kebutuhan sarana logistik rumah tangga
            if ($request->has('fasilitas') && is_array($request->fasilitas)) {
                foreach ($request->fasilitas as $item) {
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
                    ->route('pengajuan.edit', $pengajuan->id)
                    ->with('success', 'Draft pengajuan berhasil disimpan.');
            }

            return redirect()
                ->route('pengajuan.show', $pengajuan->id)
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
    public function show(Bimtek $pengajuan): View
    {
        $user = Auth::user();
        $user->load('role');

        $canAccess = $pengajuan->pic_user_id === $user->id
            || $user->isAdminIt()
            || $user->isKepala()
            || $user->isPpk()
            || $user->isRt();

        if (!$canAccess) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan ini.');
        }

        $pengajuan->load(['pic', 'fasilitasLogistiks', 'syaratDokumens']);

        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bimtek $pengajuan): View|RedirectResponse
    {
        $user = Auth::user();

        if ($pengajuan->pic_user_id !== $user->id && !$user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        if (!in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi'])) {
            return redirect()
                ->route('pengajuan.show', $pengajuan->id)
                ->with('error', 'Pengajuan tidak dapat diedit karena sudah diproses.');
        }

        // 💡 PERBAIKAN UTAMA: Eager Loading relasi syaratDokumens untuk view edit
        $pengajuan->load(['syaratDokumens', 'kebutuhanAnggarans', 'fasilitasLogistiks']);

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
    public function update(UpdatePengajuanRequest $request, Bimtek $pengajuan): RedirectResponse
    {
        $user = Auth::user();

        if ($pengajuan->pic_user_id !== $user->id && !$user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate pengajuan ini.');
        }

        if (!in_array($pengajuan->status, ['draft_pic', 'diajukan', 'perlu_revisi'])) {
            return redirect()
                ->route('pengajuan.show', $pengajuan->id)
                ->with('error', 'Pengajuan tidak dapat diupdate karena sudah diproses.');
        }

        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $isDraft = $request->has('save_draft');

            $validated['butuh_verifikasi_dokumen'] = $request->has('butuh_verifikasi_dokumen') ? 1 : 0;

            if ($isDraft) {
                $validated['status'] = 'draft_pic';
            } elseif ($pengajuan->status === 'perlu_revisi') {
                $validated['status'] = $pengajuan->kepala_approved_at ? 'disetujui_kepala' : 'diajukan';
            } elseif ($pengajuan->status === 'draft_pic') {
                $validated['status'] = 'diajukan';
            }

            $validated['mode_pelaksanaan'] = $validated['mode_pelaksanaan'] ?? 'offline';

            $validated['judul_rencana'] = $validated['judul_rencana'] ?? $request->input('judul');
            $validated['tempat_kegiatan_rencana'] = $validated['tempat_kegiatan_rencana'] ?? $request->input('tempat_kegiatan');
            $validated['tanggal_mulai_rencana'] = $validated['tanggal_mulai_rencana'] ?? $request->input('tanggal_mulai');
            $validated['tanggal_selesai_rencana'] = $validated['tanggal_selesai_rencana'] ?? $request->input('tanggal_selesai');
            $validated['deskripsi_rencana'] = $validated['deskripsi_rencana'] ?? $request->input('deskripsi');

            $pengajuan->update($validated);

            // 💡 PERBAIKAN SINKRONISASI TABEL SYARAT_DOKUMENS
            // Bersihkan baris lama terlebih dahulu
            DB::table('syarat_dokumens')->where('bimtek_id', $pengajuan->id)->delete();

            if ($validated['butuh_verifikasi_dokumen'] && $request->filled('syarat_dokumen')) {
                foreach ($request->input('syarat_dokumen') as $doc) {
                    if (empty($doc)) continue;

                    $namaDokumen = is_array($doc) ? ($doc['nama_dokumen'] ?? '') : $doc;
                    $deskripsi   = is_array($doc) ? ($doc['deskripsi_syarat'] ?? null) : null;
                    $isWajib     = is_array($doc) ? (isset($doc['is_wajib']) ? 1 : 0) : 1;

                    if (!empty($namaDokumen)) {
                        SyaratDokumen::create([
                            'bimtek_id'        => $pengajuan->id,
                            'nama_dokumen'     => $namaDokumen,
                            'deskripsi_syarat' => $deskripsi,
                            'is_wajib'         => $isWajib,
                        ]);
                    }
                }
            }

            // Sinkronisasi ulang data RAB SBM
            if ($request->has('anggaran')) {
                $pengajuan->kebutuhanAnggarans()->delete();

                foreach ($request->anggaran as $item) {
                    if (empty($item['nama_item']) && !($item['total_biaya'] ?? 0)) {
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

            // Sinkronisasi ulang data Logistik RT
            if ($request->has('fasilitas')) {
                $pengajuan->fasilitasLogistiks()->delete();

                foreach ($request->fasilitas as $item) {
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
                    ->route('pengajuan.edit', $pengajuan->id)
                    ->with('success', 'Draft pengajuan berhasil disimpan.');
            }

            return redirect()
                ->route('pengajuan.show', $pengajuan->id)
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
    public function destroy(Bimtek $pengajuan): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isAdminIt() && $pengajuan->pic_user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        if (in_array($pengajuan->status, ['disetujui_final', 'persiapan', 'berlangsung', 'selesai'])) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Pengajuan tidak dapat dihapus karena sudah diproses ke tahap pelaksanaan.');
        }

        $pengajuan->delete();

        return redirect()
            ->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}