<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPeserta;
use App\Models\Bimtek;
use App\Models\SesiAbsensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Tampilkan semua daftar sesi absensi untuk suatu kegiatan Bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);

        $bimtek->load([
            'sesiAbsensis' => function ($q) {
                $q->with('openedBy')->withCount('absensiPesertas')->latest();
            },
            'peserta',
        ]);

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);
        $user = Auth::user();

        // Pemeriksaan berkas verifikasi kelulusan peserta dari tabel jembatan baru
        $isVerified = false;
        if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
            $pivot = $bimtek->peserta()->where('user_id', $user->id)->first();
            $statusVerifikasi = $pivot?->pivot->status_verifikasi ?? 'invited';
            $isVerified = in_array($statusVerifikasi, ['verified', 'diverifikasi']);
        } else {
            $isVerified = true; 
        }

        // Ambil data riwayat log kehadiran jika aktor merupakan peserta
        $userAttendances = [];
        if ($isPeserta) {
            $userAttendances = AbsensiPeserta::where('user_id', $user->id)
                ->whereIn('sesi_absensi_id', $bimtek->sesiAbsensis->pluck('id'))
                ->pluck('sesi_absensi_id')
                ->toArray();
        }

        $totalSesi = $bimtek->sesiAbsensis->count();
        $totalPeserta = $bimtek->peserta->count();

        return view('absensi.index', compact('bimtek', 'canManage', 'isPeserta', 'userAttendances', 'totalSesi', 'totalPeserta', 'isVerified'));
    }

    /**
     * Form pembuatan sesi absensi baru.
     */
    public function create(Bimtek $bimtek): View
    {
        $this->authorizeManage($bimtek);

        return view('absensi.create', compact('bimtek'));
    }

    /**
     * Simpan sesi absensi baru ke database.
     */
    public function store(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'status' => 'required|in:terbuka,ditutup',
        ], [
            'nama_sesi.required' => 'Nama sesi wajib diisi.',
            'status.required' => 'Status sesi wajib dipilih.',
        ]);

        SesiAbsensi::create([
            'bimtek_id' => $bimtek->id,
            'nama_sesi' => $validated['nama_sesi'],
            'status' => $validated['status'],
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('bimtek.absensi.index', $bimtek->id)
            ->with('success', 'Sesi absensi berhasil dibuat.');
    }

    /**
     * Tampilkan detail lembar absensi per sesi.
     */
    public function show(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $this->authorizeAccess($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $sesi->load(['openedBy', 'absensiPesertas.peserta']);
        $bimtek->load('peserta');

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);
        $user = Auth::user();

        $hasAttended = $sesi->absensiPesertas->where('user_id', $user->id)->count() > 0;

        // Mencari daftar peserta yang tercatat mangkir / belum melakukan presensi
        $attendedUserIds = $sesi->absensiPesertas->pluck('user_id')->toArray();
        $notAttendedPeserta = $bimtek->peserta->whereNotIn('id', $attendedUserIds);
        
        $totalPeserta = $bimtek->peserta->count();

        return view('absensi.show', compact('bimtek', 'sesi', 'canManage', 'isPeserta', 'hasAttended', 'notAttendedPeserta', 'totalPeserta'));
    }

    /**
     * Form ubah data sesi absensi.
     */
    public function edit(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        return view('absensi.edit', compact('bimtek', 'sesi'));
    }

    /**
     * Update data sesi absensi.
     */
    public function update(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'status' => 'required|in:terbuka,ditutup',
        ], [
            'nama_sesi.required' => 'Nama sesi wajib diisi.',
            'status.required' => 'Status sesi wajib dipilih.',
        ]);

        $sesi->update([
            'nama_sesi' => $validated['nama_sesi'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('bimtek.absensi.show', [$bimtek->id, $sesi->id])
            ->with('success', 'Sesi absensi berhasil diperbarui.');
    }

    /**
     * Hapus sesi absensi.
     */
    public function destroy(Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $sesi->delete();

        return redirect()
            ->route('bimtek.absensi.index', $bimtek->id)
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }

    /**
     * Buka / Tutup Sesi Absensi secara kilat.
     */
    public function toggleStatus(Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $newStatus = $sesi->isOpen() ? 'ditutup' : 'terbuka';
        $sesi->update(['status' => $newStatus]);

        if ($newStatus === 'terbuka') {
            $sesi->generateQrCode();
        }

        $message = $newStatus === 'terbuka'
            ? 'Sesi absensi berhasil dibuka. QR Code telah digenerate.'
            : 'Sesi absensi berhasil ditutup.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Tampilkan layar monitor QR Code (Sisi Pandangan Panitia/Proyektor).
     */
    public function showQr(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->mode_pelaksanaan === 'online') {
            abort(404);
        }

        if (! $sesi->qr_code) {
            $sesi->generateQrCode();
        }

        return view('absensi.show-qr', compact('bimtek', 'sesi'));
    }

    /**
     * Tampilkan kamera / antarmuka scan QR bagi peserta (Sisi Handphone Peserta).
     */
    public function scanInterface(Bimtek $bimtek, SesiAbsensi $sesi): View|RedirectResponse
    {
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta resmi dari kegiatan bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->mode_pelaksanaan === 'online') {
            return Redirect::route('bimtek.absensi.show', [$bimtek->id, $sesi->id])
                ->with('error', 'Bimtek mode online menggunakan presensi langsung. Silakan klik tombol Hadir Online.');
        }

        $hasAttended = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('absensi.scan-qr', compact('bimtek', 'sesi', 'hasAttended'));
    }

    /**
     * Validasi kode QR hasil scan kamera handphone peserta.
     */
    public function scanQr(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->mode_pelaksanaan === 'online') {
            return redirect()
                ->route('bimtek.absensi.show', [$bimtek->id, $sesi->id])
                ->with('error', 'Bimtek mode online tidak menggunakan scan QR. Gunakan tombol Hadir Online.');
        }

        $validated = $request->validate(['qr_code' => 'required|string']);

        if ($bimtek->status !== 'berlangsung') {
            return redirect()->back()->with('error', 'Absensi hanya dapat dilakukan saat bimtek sedang berlangsung.');
        }

        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Anda sudah tercatat hadir pada sesi ini.');
        }

        if (! $sesi->isQrCodeValid($validated['qr_code'])) {
            return redirect()->back()->with('error', 'QR Code tidak valid atau sudah kadaluarsa.');
        }

        // DISIMPAN MENGGUNAKAN QUERY BUILDER AGAR AMAN DENGAN COMPOSITE PK
        DB::table('absensi_pesertas')->insert([
            'sesi_absensi_id'  => $sesi->id,
            'bimtek_id'        => $bimtek->id,
            'user_id'          => Auth::id(),
            'status_kehadiran' => 'hadir',
            'waktu_presensi'   => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()
            ->route('bimtek.absensi.index', $bimtek->id)
            ->with('success', 'Kehadiran Anda berhasil dicatat melalui QR Code.');
    }

    /**
     * Presensi mandiri bagi peserta kelas Online / Hybrid.
     */
    public function hadirOnline(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if (! in_array($bimtek->mode_pelaksanaan, ['online', 'hybrid'])) {
            return redirect()->back()->with('error', 'Presensi online hanya tersedia untuk bimtek mode online atau hybrid.');
        }

        if ($bimtek->status !== 'berlangsung') {
            return redirect()->back()->with('error', 'Absensi hanya dapat dilakukan saat bimtek sedang berlangsung.');
        }

        if (! $sesi->isOpen()) {
            return redirect()->back()->with('error', 'Sesi absensi masih ditutup oleh pihak panitia pelaksana.');
        }

        $validated = $request->validate([
            'bukti_hadir_online' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ], [
            'bukti_hadir_online.required' => 'Screenshot bukti kehadiran ruang virtual wajib diunggah.',
        ]);

        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Anda sudah tercatat hadir pada sesi ini.');
        }

        $buktiPath = $validated['bukti_hadir_online']->store('absensi-bukti-online', 'public');

        // DISIMPAN MENGGUNAKAN QUERY BUILDER AGAR AMAN DENGAN COMPOSITE PK
        DB::table('absensi_pesertas')->insert([
            'sesi_absensi_id'         => $sesi->id,
            'bimtek_id'               => $bimtek->id,
            'user_id'                 => Auth::id(),
            'bukti_hadir_online_path' => $buktiPath,
            'status_kehadiran'        => 'hadir',
            'waktu_presensi'          => now(),
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect()
            ->route('bimtek.absensi.index', $bimtek->id)
            ->with('success', 'Kehadiran online Anda berhasil disimpan.');
    }

    /**
     * Tindakan Force-Presence (Penyuntikan tanda hadir manual oleh Panitia).
     */
    public function tambahKehadiran(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $validated = $request->validate(['user_id' => 'required|exists:users,id']);

        $isPesertaBimtek = $bimtek->peserta()->where('user_id', $validated['user_id'])->exists();
        if (! $isPesertaBimtek) {
            return redirect()->back()->with('error', 'Pegawai tersebut bukan peserta resmi bimtek ini.');
        }

        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'Peserta sudah tercatat hadir pada sesi ini.');
        }

        // DISIMPAN MENGGUNAKAN QUERY BUILDER AGAR AMAN DENGAN COMPOSITE PK
        DB::table('absensi_pesertas')->insert([
            'sesi_absensi_id'  => $sesi->id,
            'bimtek_id'        => $bimtek->id,
            'user_id'          => $validated['user_id'],
            'status_kehadiran' => 'hadir',
            'waktu_presensi'   => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->back()->with('success', 'Kehadiran peserta berhasil ditambahkan manual.');
    }

    /**
     * Batalkan rekor absensi kehadiran peserta oleh panitia.
     */
    public function hapusKehadiran(Bimtek $bimtek, SesiAbsensi $sesi, string $userId): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        // Hapus menggunakan Query Builder karena menggunakan Composite Primary Key
        DB::table('absensi_pesertas')
            ->where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->back()->with('success', 'Rekor kehadiran peserta berhasil dihapus.');
    }

    /**
     * Rekapitulasi Matriks Absensi Total.
     */
    public function rekap(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);

        $bimtek->load([
            'sesiAbsensis' => function ($q) {
                $q->orderBy('created_at');
            },
            'peserta',
        ]);

        $canManage = $this->canManage($bimtek);
        $sesiIds = $bimtek->sesiAbsensis->pluck('id');
        $pesertaIds = $bimtek->peserta->pluck('id');

        $allAttendances = AbsensiPeserta::whereIn('user_id', $pesertaIds)
            ->whereIn('sesi_absensi_id', $sesiIds)
            ->get()
            ->groupBy('user_id');

        $rekapData = [];
        foreach ($bimtek->peserta as $peserta) {
            $attendances = $allAttendances->get($peserta->id)?->pluck('sesi_absensi_id')->toArray() ?? [];

            $rekapData[$peserta->id] = [
                'peserta' => $peserta,
                'attendances' => $attendances,
                'total_hadir' => count($attendances),
                'persentase' => $bimtek->sesiAbsensis->count() > 0
                    ? round((count($attendances) / $bimtek->sesiAbsensis->count()) * 100, 1)
                    : 0,
            ];
        }

        uasort($rekapData, fn($a, $b) => strcmp($a['peserta']->name, $b['peserta']->name));
        $syaratKehadiran = $bimtek->syarat_kehadiran_persen ?? 80;

        return view('absensi.rekap', compact('bimtek', 'rekapData', 'canManage', 'syaratKehadiran'));
    }

    private function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();

        return $isPic || $isPanitia;
    }

    private function isPeserta(Bimtek $bimtek): bool
    {
        return $bimtek->peserta()->where('user_id', Auth::id())->exists();
    }

    private function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();

        if (! $isPic && ! $isPanitia && ! $isPeserta) {
            abort(403, 'Anda tidak memiliki hak akses informasi absensi pada kegiatan ini.');
        }
    }

    private function authorizeManage(Bimtek $bimtek): void
    {
        if (! $this->canManage($bimtek)) {
            abort(403, 'Wewenang terbatas! Hanya PIC atau Panitia Pokja yang dapat mengelola lembar absensi.');
        }
    }
}