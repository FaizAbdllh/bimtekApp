<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPeserta;
use App\Models\Bimtek;
use App\Models\SesiAbsensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Display a listing of sesi absensi for a bimtek.
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

        // Get user's attendance if peserta
        $userAttendances = [];
        if ($isPeserta) {
            $userAttendances = AbsensiPeserta::where('user_id', $user->id)
                ->whereIn('sesi_absensi_id', $bimtek->sesiAbsensis->pluck('id'))
                ->pluck('sesi_absensi_id')
                ->toArray();
        }

        // Calculate attendance statistics
        $totalSesi = $bimtek->sesiAbsensis->count();
        $totalPeserta = $bimtek->peserta->count();

        return view('absensi.index', compact('bimtek', 'canManage', 'isPeserta', 'userAttendances', 'totalSesi', 'totalPeserta', 'isVerified'));
    }

    /**
     * Show the form for creating a new sesi absensi.
     */
    public function create(Bimtek $bimtek): View
    {
        $this->authorizeManage($bimtek);

        return view('absensi.create', compact('bimtek'));
    }

    /**
     * Store a newly created sesi absensi.
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
            ->route('bimtek.absensi.index', $bimtek)
            ->with('success', 'Sesi absensi berhasil dibuat.');
    }

    /**
     * Display the specified sesi absensi with attendance list.
     */
    public function show(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $this->authorizeAccess($bimtek);

        // Make sure sesi belongs to this bimtek
        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $sesi->load(['openedBy', 'absensiPesertas.peserta']);
        $bimtek->load('peserta');

        $canManage = $this->canManage($bimtek);
        $isPeserta = $this->isPeserta($bimtek);
        $user = Auth::user();

        // Check if current user has attended this session
        $hasAttended = $sesi->absensiPesertas->where('user_id', $user->id)->count() > 0;

        // Get list of peserta who haven't attended
        $attendedUserIds = $sesi->absensiPesertas->pluck('user_id')->toArray();
        $notAttendedPeserta = $bimtek->peserta->whereNotIn('id', $attendedUserIds);

        return view('absensi.show', compact('bimtek', 'sesi', 'canManage', 'isPeserta', 'hasAttended', 'notAttendedPeserta'));
    }

    /**
     * Show the form for editing the specified sesi absensi.
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
     * Update the specified sesi absensi.
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
            ->route('bimtek.absensi.show', [$bimtek, $sesi])
            ->with('success', 'Sesi absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified sesi absensi.
     */
    public function destroy(Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $sesi->delete();

        return redirect()
            ->route('bimtek.absensi.index', $bimtek)
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }

    /**
     * Toggle sesi status (buka/tutup).
     */
    public function toggleStatus(Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $newStatus = $sesi->isOpen() ? 'ditutup' : 'terbuka';
        $sesi->update(['status' => $newStatus]);

        // Generate QR code when opening session
        if ($newStatus === 'terbuka') {
            $sesi->generateQrCode();
        }

        $message = $newStatus === 'terbuka'
            ? 'Sesi absensi berhasil dibuka. QR Code telah digenerate.'
            : 'Sesi absensi berhasil ditutup.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Show QR code for active session (Panitia view).
     * QR is only generated when session is open.
     */
    public function showQr(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->isOnlineOnlyMode()) {
            abort(404);
        }

        // Generate QR only if it doesn't exist yet (first time opening session)
        // QR persists until session is closed
        if (! $sesi->qr_code) {
            $sesi->generateQrCode();
        }

        return view('absensi.show-qr', compact('bimtek', 'sesi'));
    }

    /**
     * Show scan interface for peserta.
     */
    public function scanInterface(Bimtek $bimtek, SesiAbsensi $sesi): View
    {
        $user = Auth::user();

        // Check if user is peserta
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->isOnlineOnlyMode()) {
            return redirect()
                ->route('bimtek.absensi.show', [$bimtek, $sesi])
                ->with('error', 'Bimtek mode online menggunakan presensi langsung. Silakan klik tombol Hadir Online.');
        }

        // Check if already attended
        $hasAttended = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $user->id)
            ->exists();

        return view('absensi.scan-qr', compact('bimtek', 'sesi', 'hasAttended'));
    }

    /**
     * Validate scanned QR and record attendance.
     */
    public function scanQr(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $user = Auth::user();

        // Check if user is peserta
        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($bimtek->isOnlineOnlyMode()) {
            return redirect()
                ->route('bimtek.absensi.show', [$bimtek, $sesi])
                ->with('error', 'Bimtek mode online tidak menggunakan scan QR. Gunakan tombol Hadir Online.');
        }

        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Check if bimtek is active
        if ($bimtek->status_pelaksanaan !== 'berlangsung') {
            return redirect()
                ->back()
                ->with('error', 'Absensi hanya dapat dilakukan saat bimtek sedang berlangsung.');
        }

        // Check if already attended
        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('info', 'Anda sudah tercatat hadir pada sesi ini.');
        }

        // Validate QR code
        if (! $sesi->isQrCodeValid($validated['qr_code'])) {
            return redirect()
                ->back()
                ->with('error', 'QR Code tidak valid atau sudah kadaluarsa. Silakan minta QR Code baru dari panitia.');
        }

        // Record attendance
        AbsensiPeserta::create([
            'sesi_absensi_id' => $sesi->id,
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('bimtek.absensi.show', [$bimtek, $sesi])
            ->with('success', 'Kehadiran Anda berhasil dicatat melalui QR Code.');
    }

    /**
     * Peserta attendance for online/hybrid sessions.
     */
    public function hadirOnline(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $user = Auth::user();

        if (! $this->isPeserta($bimtek)) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if (! $bimtek->supportsOnlineAttendance()) {
            return redirect()
                ->back()
                ->with('error', 'Presensi online hanya tersedia untuk bimtek mode online atau hybrid.');
        }

        if ($bimtek->status_pelaksanaan !== 'berlangsung') {
            return redirect()
                ->back()
                ->with('error', 'Absensi hanya dapat dilakukan saat bimtek sedang berlangsung.');
        }

        if (! $sesi->isOpen()) {
            return redirect()
                ->back()
                ->with('error', 'Sesi absensi masih ditutup. Silakan tunggu panitia membuka sesi.');
        }

        $validated = $request->validate([
            'bukti_hadir_online' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ], [
            'bukti_hadir_online.required' => 'Screenshot kehadiran wajib diunggah untuk absensi online.',
            'bukti_hadir_online.image' => 'File bukti harus berupa gambar.',
            'bukti_hadir_online.mimes' => 'Format gambar bukti harus JPG, JPEG, atau PNG.',
            'bukti_hadir_online.max' => 'Ukuran gambar bukti maksimal 4MB.',
        ]);

        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('info', 'Anda sudah tercatat hadir pada sesi ini.');
        }

        $buktiPath = $validated['bukti_hadir_online']->store('absensi-bukti-online', 'public');

        AbsensiPeserta::create([
            'sesi_absensi_id' => $sesi->id,
            'user_id' => $user->id,
            'bukti_hadir_online_path' => $buktiPath,
        ]);

        return redirect()
            ->route('bimtek.absensi.show', [$bimtek, $sesi])
            ->with('success', 'Kehadiran Anda berhasil dicatat melalui presensi online.');
    }

    /**
     * PIC/Panitia manually add attendance for a peserta.
     */
    public function tambahKehadiran(Request $request, Bimtek $bimtek, SesiAbsensi $sesi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user is peserta of this bimtek
        $isPesertaBimtek = $bimtek->peserta()->where('users.id', $validated['user_id'])->exists();
        if (! $isPesertaBimtek) {
            return redirect()
                ->back()
                ->with('error', 'User bukan peserta bimtek ini.');
        }

        // Check if already attended
        $exists = AbsensiPeserta::where('sesi_absensi_id', $sesi->id)
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('info', 'Peserta sudah tercatat hadir pada sesi ini.');
        }

        // Record attendance
        AbsensiPeserta::create([
            'sesi_absensi_id' => $sesi->id,
            'user_id' => $validated['user_id'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Kehadiran peserta berhasil ditambahkan.');
    }

    /**
     * PIC/Panitia remove attendance for a peserta.
     */
    public function hapusKehadiran(Bimtek $bimtek, SesiAbsensi $sesi, AbsensiPeserta $absensi): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        if ($sesi->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if ($absensi->sesi_absensi_id !== $sesi->id) {
            abort(404);
        }

        $absensi->delete();

        return redirect()
            ->back()
            ->with('success', 'Kehadiran peserta berhasil dihapus.');
    }

    /**
     * Display rekap absensi for all peserta.
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

        // Pre-load all attendances to avoid N+1 query
        $sesiIds = $bimtek->sesiAbsensis->pluck('id');
        $pesertaIds = $bimtek->peserta->pluck('id');

        $allAttendances = AbsensiPeserta::whereIn('user_id', $pesertaIds)
            ->whereIn('sesi_absensi_id', $sesiIds)
            ->get()
            ->groupBy('user_id');

        // Build attendance matrix
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

        // Sort by name
        uasort($rekapData, function ($a, $b) {
            return strcmp($a['peserta']->name, $b['peserta']->name);
        });

        $syaratKehadiran = $bimtek->syarat_kehadiran_persen ?? 80;

        return view('absensi.rekap', compact('bimtek', 'rekapData', 'canManage', 'syaratKehadiran'));
    }

    /**
     * Check if user can manage absensi (PIC or Panitia).
     */
    private function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();

        // Check if PIC
        $isPic = $bimtek->pic_user_id === $user->id;

        // Check if Panitia
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        return $isPic || $isPanitia;
    }

    /**
     * Check if user is peserta of this bimtek.
     */
    private function isPeserta(Bimtek $bimtek): bool
    {
        return $bimtek->peserta()->where('users.id', Auth::id())->exists();
    }

    /**
     * Check if user has access to this bimtek's absensi.
     */
    private function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Check if PIC
        $isPic = $bimtek->pic_user_id === $user->id;

        // Check if in pivot table (Panitia or Peserta)
        $hasAccess = $bimtek->users()->where('users.id', $user->id)->exists();

        if (! $isPic && ! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }
    }

    /**
     * Check if user can manage this bimtek (PIC or Panitia).
     */
    private function authorizeManage(Bimtek $bimtek): void
    {
        if (! $this->canManage($bimtek)) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola absensi.');
        }
    }
}
