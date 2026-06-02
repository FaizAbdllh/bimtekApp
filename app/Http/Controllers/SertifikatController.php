<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPeserta;
use App\Models\Bimtek;
use App\Models\PengumpulanTugas;
use App\Models\Sertifikat;
use App\Services\SertifikatTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SertifikatController extends Controller
{
    /**
     * Display a listing of sertifikat for a bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->ensureHasSertifikat($bimtek);
        $this->authorizeAccess($bimtek);

        $bimtek->load([
            'sertifikats.user',
            'peserta',
            'sesiAbsensis',
            'tugas',
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

        // Get eligibility data for all peserta
        $eligibilityData = $this->getEligibilityData($bimtek);

        // Get user's sertifikat if peserta
        $userSertifikat = null;
        if ($isPeserta) {
            $userSertifikat = $bimtek->sertifikats->where('user_id', $user->id)->first();
        }

        return view('sertifikat.index', compact(
            'bimtek',
            'canManage',
            'isPeserta',
            'eligibilityData',
            'userSertifikat',
            'isVerified'
        ));
    }

    /**
     * Generate sertifikat for eligible peserta using standard template.
     */
    public function generate(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->ensureHasSertifikat($bimtek);
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'peserta_ids' => 'required|array|min:1',
            'peserta_ids.*' => 'exists:users,id',
            'tanggal_terbit' => 'required|date',
        ], [
            'peserta_ids.required' => 'Pilih minimal 1 peserta.',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi.',
        ]);

        $tanggalTerbit = $validated['tanggal_terbit'];
        $generatedCount = 0;
        $skippedCount = 0;
        $nextSequence = $this->getMaxSequenceForPeriod($bimtek, $tanggalTerbit);

        foreach ($validated['peserta_ids'] as $pesertaId) {
            // Check if sertifikat already exists
            $exists = Sertifikat::where('bimtek_id', $bimtek->id)
                ->where('user_id', $pesertaId)
                ->exists();

            if ($exists) {
                $skippedCount++;

                continue;
            }

            // Check if peserta is part of this bimtek
            $isPesertaBimtek = $bimtek->peserta()->where('users.id', $pesertaId)->exists();
            if (! $isPesertaBimtek) {
                $skippedCount++;

                continue;
            }

            $created = false;

            // Retry to handle rare race condition on unique nomor_sertifikat
            for ($attempt = 0; $attempt < 5; $attempt++) {
                $nextSequence++;
                $nomorSertifikat = $this->generateNomorSertifikat($bimtek, $tanggalTerbit, $nextSequence);

                // Generate PDF from standard template
                $filePath = $this->generateSertifikatFile($bimtek, $pesertaId, $nomorSertifikat, $tanggalTerbit);
                if (! $filePath) {
                    break;
                }

                try {
                    Sertifikat::create([
                        'bimtek_id' => $bimtek->id,
                        'user_id' => $pesertaId,
                        'nomor_sertifikat' => $nomorSertifikat,
                        'tanggal_terbit' => $tanggalTerbit,
                        'file_path' => $filePath,
                    ]);

                    $generatedCount++;
                    $created = true;
                    break;
                } catch (QueryException $e) {
                    Storage::disk('public')->delete($filePath);

                    if (! $this->isDuplicateNomorException($e)) {
                        throw $e;
                    }
                }
            }

            if (! $created) {
                $skippedCount++;
            }
        }

        $message = "Berhasil generate {$generatedCount} sertifikat.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} peserta dilewati (sudah punya sertifikat).";
        }

        return redirect()
            ->route('bimtek.sertifikat.index', $bimtek)
            ->with('success', $message);
    }

    /**
     * Download sertifikat.
     */
    public function download(Bimtek $bimtek, Sertifikat $sertifikat): BinaryFileResponse
    {
        $this->ensureHasSertifikat($bimtek);
        $this->authorizeAccess($bimtek);

        // Check ownership
        $user = Auth::user();
        $canManage = $this->canManage($bimtek);

        if (! $canManage && $sertifikat->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }

        if ($sertifikat->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if (! $sertifikat->file_path || ! Storage::disk('public')->exists($sertifikat->file_path)) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        $nomorClean = str_replace(['/', '\\'], '-', $sertifikat->nomor_sertifikat);
        $extension = pathinfo($sertifikat->file_path, PATHINFO_EXTENSION);

        // Better filename format: sertifikat_nomor_nama_tanggal.ext
        $peserta = $sertifikat->user;
        $namaPeserta = strtolower(str_replace(' ', '_', $peserta->name));
        $tanggal = $sertifikat->tanggal_terbit->format('d-m-Y');
        $filename = "sertifikat_{$nomorClean}_{$namaPeserta}_{$tanggal}.{$extension}";

        $path = Storage::disk('public')->path($sertifikat->file_path);

        return response()->download($path, $filename);
    }

    /**
     * Preview sertifikat.
     */
    public function preview(Bimtek $bimtek, Sertifikat $sertifikat)
    {
        $this->ensureHasSertifikat($bimtek);
        $this->authorizeAccess($bimtek);

        // Check ownership
        $user = Auth::user();
        $canManage = $this->canManage($bimtek);

        if (! $canManage && $sertifikat->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }

        if ($sertifikat->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        if (! $sertifikat->file_path || ! Storage::disk('public')->exists($sertifikat->file_path)) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($sertifikat->file_path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Delete sertifikat.
     */
    public function destroy(Bimtek $bimtek, Sertifikat $sertifikat): RedirectResponse
    {
        $this->ensureHasSertifikat($bimtek);
        $this->authorizeManage($bimtek);

        if ($sertifikat->bimtek_id !== $bimtek->id) {
            abort(404);
        }

        // Delete file
        if ($sertifikat->file_path) {
            Storage::disk('public')->delete($sertifikat->file_path);
        }

        $sertifikat->delete();

        return redirect()
            ->route('bimtek.sertifikat.index', $bimtek)
            ->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Get eligibility data for all peserta.
     */
    private function getEligibilityData(Bimtek $bimtek): array
    {
        $data = [];
        $syaratKehadiran = $bimtek->syarat_kehadiran_persen ?? 80;
        $syaratTugas = $bimtek->syarat_tugas_persen ?? 80;
        $syaratTugasWajib = $bimtek->syarat_tugas_wajib ?? false;

        $totalSesi = $bimtek->sesiAbsensis->count();
        $totalTugas = $bimtek->tugas->count();

        // Pre-load all data to avoid N+1 queries
        $sesiIds = $bimtek->sesiAbsensis->pluck('id');
        $tugasIds = $bimtek->tugas->pluck('id');
        $pesertaIds = $bimtek->peserta->pluck('id');

        $allAttendances = AbsensiPeserta::whereIn('user_id', $pesertaIds)
            ->whereIn('sesi_absensi_id', $sesiIds)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->count());

        $allSubmissions = PengumpulanTugas::whereIn('user_id', $pesertaIds)
            ->whereIn('tugas_id', $tugasIds)
            ->get()
            ->groupBy('user_id');

        foreach ($bimtek->peserta as $peserta) {
            // Calculate kehadiran
            $hadirCount = $allAttendances->get($peserta->id, 0);
            $persentaseKehadiran = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 1) : 0;
            $lulusKehadiran = $persentaseKehadiran >= $syaratKehadiran;

            // Calculate tugas
            $submissionsByPeserta = $allSubmissions->get($peserta->id, collect());
            $tugasTerkumpul = $submissionsByPeserta->count();
            $tugasDinilai = $submissionsByPeserta->whereNotNull('nilai')->count();
            $rataRataNilaiTugas = $tugasDinilai > 0
                ? round((float) $submissionsByPeserta->whereNotNull('nilai')->avg('nilai'), 1)
                : null;

            // Check tugas eligibility
            $lulusTugas = true;
            $lulusKelengkapanTugas = true;
            $lulusNilaiTugas = true;
            if ($syaratTugasWajib && $totalTugas > 0) {
                $lulusKelengkapanTugas = $tugasTerkumpul >= $totalTugas;
                $lulusNilaiTugas = $rataRataNilaiTugas !== null && $rataRataNilaiTugas >= $syaratTugas;
                $lulusTugas = $lulusKelengkapanTugas && $lulusNilaiTugas;
            }

            // Overall eligibility
            $eligible = $lulusKehadiran && $lulusTugas;

            // Check if already has sertifikat
            $hasSertifikat = $bimtek->sertifikats->where('user_id', $peserta->id)->count() > 0;

            $data[$peserta->id] = [
                'peserta' => $peserta,
                'hadir_count' => $hadirCount,
                'total_sesi' => $totalSesi,
                'persentase_kehadiran' => $persentaseKehadiran,
                'lulus_kehadiran' => $lulusKehadiran,
                'tugas_terkumpul' => $tugasTerkumpul,
                'tugas_dinilai' => $tugasDinilai,
                'total_tugas' => $totalTugas,
                'rata_rata_nilai_tugas' => $rataRataNilaiTugas,
                'lulus_kelengkapan_tugas' => $lulusKelengkapanTugas,
                'lulus_nilai_tugas' => $lulusNilaiTugas,
                'lulus_tugas' => $lulusTugas,
                'eligible' => $eligible,
                'has_sertifikat' => $hasSertifikat,
                'sertifikat' => $bimtek->sertifikats->where('user_id', $peserta->id)->first(),
            ];
        }

        // Sort by name
        uasort($data, function ($a, $b) {
            return strcmp($a['peserta']->name, $b['peserta']->name);
        });

        return $data;
    }

    /**
     * Generate nomor sertifikat.
     */
    private function generateNomorSertifikat(Bimtek $bimtek, string $tanggalTerbit, int $sequence): string
    {
        $year = date('Y', strtotime($tanggalTerbit));
        $month = date('m', strtotime($tanggalTerbit));

        // Format: SEQ/SERT-BIMTEK/BIMTEK_ID/MONTH/YEAR
        return sprintf('%03d/SERT-BIMTEK/%s/%s/%s', $sequence, (string) $bimtek->id, $month, $year);
    }

    /**
     * Get current max sequence number for same bimtek and period.
     */
    private function getMaxSequenceForPeriod(Bimtek $bimtek, string $tanggalTerbit): int
    {
        $year = date('Y', strtotime($tanggalTerbit));
        $month = date('m', strtotime($tanggalTerbit));
        $suffix = '/SERT-BIMTEK/'.(string) $bimtek->id.'/'.$month.'/'.$year;

        $max = 0;
        $nomors = Sertifikat::where('bimtek_id', $bimtek->id)
            ->where('nomor_sertifikat', 'like', '%'.$suffix)
            ->pluck('nomor_sertifikat');

        foreach ($nomors as $nomor) {
            $parts = explode('/', $nomor);
            if (count($parts) === 5 && ctype_digit($parts[0])) {
                $max = max($max, (int) $parts[0]);
            }
        }

        return $max;
    }

    /**
     * Check if DB exception is duplicate unique nomor_sertifikat.
     */
    private function isDuplicateNomorException(QueryException $e): bool
    {
        return $e->getCode() === '23000'
            && str_contains($e->getMessage(), 'sertifikats_nomor_sertifikat_unique');
    }

    /**
     * Abort if bimtek has sertifikat disabled.
     */
    private function ensureHasSertifikat(Bimtek $bimtek): void
    {
        if (! $bimtek->has_sertifikat) {
            abort(404, 'Fitur Sertifikat dinonaktifkan untuk bimtek ini.');
        }
    }

    /**
     * Generate sertifikat file as PDF using standard template and DomPDF.
     */
    private function generateSertifikatFile(Bimtek $bimtek, $pesertaId, string $nomorSertifikat, string $tanggalTerbit): ?string
    {
        $peserta = $bimtek->peserta()->where('users.id', $pesertaId)->first();
        if (! $peserta) {
            return null;
        }

        // Create directory if not exists
        $directory = "sertifikat/bimtek-{$bimtek->id}";
        Storage::disk('public')->makeDirectory($directory);
        $safeNomor = str_replace(['/', '\\'], '-', $nomorSertifikat);

        try {
            // Generate HTML from template with variable replacement
            $html = $this->renderTemplateWithData($peserta, $bimtek, $nomorSertifikat, $tanggalTerbit);

            // Generate PDF in landscape orientation
            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            $pdfOutput = $pdf->output();

            // Ensure output is valid PDF content
            if ($pdfOutput !== '' && str_starts_with($pdfOutput, '%PDF')) {
                $filename = "sertifikat_{$safeNomor}_{$pesertaId}.pdf";
                $filePath = "{$directory}/{$filename}";
                Storage::disk('public')->put($filePath, $pdfOutput);

                Log::debug('Sertifikat PDF generated', [
                    'bimtek_id' => $bimtek->id,
                    'peserta_id' => $pesertaId,
                    'file_path' => $filePath,
                    'size' => strlen($pdfOutput),
                ]);

                return $filePath;
            }
        } catch (\Throwable $e) {
            Log::error('PDF generation failed', [
                'bimtek_id' => $bimtek->id,
                'peserta_id' => $pesertaId,
                'error' => $e->getMessage(),
            ]);
            \App\Models\LogSistem::error(
                "Gagal generate PDF sertifikat untuk peserta {$pesertaId} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                Auth::id()
            );
        }

        Log::error('Unable to generate sertifikat as PDF', [
            'bimtek_id' => $bimtek->id,
            'peserta_id' => $pesertaId,
            'nomor_sertifikat' => $nomorSertifikat,
        ]);
        \App\Models\LogSistem::error(
            "Output PDF sertifikat tidak valid untuk peserta {$pesertaId} pada bimtek {$bimtek->id} (nomor: {$nomorSertifikat})",
            Auth::id()
        );

        return null;
    }

    /**
     * Render template dengan mengganti variable placeholder dengan data actual
     */
    private function renderTemplateWithData($peserta, Bimtek $bimtek, string $nomorSertifikat, string $tanggalTerbit): string
    {
        $tanggalTerbitDate = \Carbon\Carbon::parse($tanggalTerbit);

        $replacements = [
            '{PESERTA}' => strtoupper($peserta->name),
            '{NIP}' => $peserta->nip ?? '-',
            '{INSTANSI}' => $peserta->instansi ?? '-',
            '{NOMOR}' => $nomorSertifikat,
            '{JUDUL}' => $bimtek->judul_final,
            '{MULAI}' => $bimtek->tanggal_mulai_aktual?->translatedFormat('d F Y') ?? '-',
            '{SELESAI}' => $bimtek->tanggal_selesai_aktual?->translatedFormat('d F Y') ?? '-',
            '{LOKASI}' => $bimtek->lokasi_aktual ?? '-',
            '{TERBIT}' => $tanggalTerbitDate->translatedFormat('d F Y'),
        ];

        $html = SertifikatTemplateService::renderAsHtml($peserta, $bimtek, $nomorSertifikat, $tanggalTerbit);

        return strtr($html, $replacements);
    }

    /**
     * Check if user can manage sertifikat (PIC or Panitia).
     */
    private function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();

        return $bimtek->pic()->where('users.id', $user->id)->exists() ||
               $bimtek->panitia()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if user is peserta of this bimtek.
     */
    private function isPeserta(Bimtek $bimtek): bool
    {
        return $bimtek->peserta()->where('users.id', Auth::id())->exists();
    }

    /**
     * Check if user has access to this bimtek.
     */
    private function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        $hasAccess = $bimtek->users()->where('users.id', $user->id)->exists();

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }
    }

    /**
     * Check if user can manage this bimtek (PIC or Panitia).
     */
    private function authorizeManage(Bimtek $bimtek): void
    {
        if (! $this->canManage($bimtek)) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola sertifikat.');
        }
    }
}
