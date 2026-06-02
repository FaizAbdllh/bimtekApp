<?php

namespace App\Http\Controllers;

use App\Mail\DokumenVerifiedRejectedMail;
use App\Models\Bimtek;
use App\Models\DokumenPersyaratanPeserta;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VerifikasiDokumenController extends Controller
{
    /**
     * Show upload dokumen form for peserta.
     */
    public function uploadForm(Bimtek $bimtek): View
    {
        $user = Auth::user();

        // Check if user is peserta of this bimtek
        $isPeserta = $bimtek->peserta()->where('users.id', $user->id)->exists();
        if (! $isPeserta) {
            abort(403, 'Anda bukan peserta bimtek ini.');
        }

        // Get status verifikasi
        $assignment = DB::table('bimtek_user')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->first();

        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        $userDokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->whereIn('jenis_dokumen', $jenisDokumenWajib)
            ->latest('uploaded_at')
            ->get();

        $dokumenMap = $userDokumen
            ->groupBy('jenis_dokumen')
            ->map(fn ($items) => $items->first());

        return view('verifikasi-dokumen.upload', compact(
            'bimtek',
            'assignment',
            'dokumenMap',
            'jenisDokumenWajib',
            'userDokumen'
        ));
    }

    /**
     * Store uploaded dokumen.
     */
    public function upload(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $user = Auth::user();
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        // Validate
        $validated = $request->validate([
            'jenis_dokumen' => ['required', Rule::in($jenisDokumenWajib)],
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
        ], [
            'file.max' => 'Ukuran file maksimal 2MB.',
            'file.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
        ]);

        // Check if old document exists and delete it
        $oldDokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->where('jenis_dokumen', $validated['jenis_dokumen'])
            ->first();

        if ($oldDokumen) {
            // Delete old file from storage
            Storage::disk('public')->delete($oldDokumen->file_path);
            // Delete old record
            $oldDokumen->delete();
        }

        // Store file
        $file = $request->file('file');
        $fileName = time().'_'.Str::slug($validated['jenis_dokumen']).'_'.$user->id.'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('dokumen_persyaratan', $fileName, 'public');

        // Create dokumen record
        DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        // Update status verifikasi to 'pending' if still 'invited' or 'rejected'
        DB::table('bimtek_user')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->whereIn('status_verifikasi', ['invited', 'rejected'])
            ->update(['status_verifikasi' => 'pending']);

        $jenisLabel = Str::of($validated['jenis_dokumen'])->replace('_', ' ')->title();

        return redirect()
            ->route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
            ->with('success', "Dokumen {$jenisLabel} berhasil diunggah. Menunggu verifikasi dari panitia.");
    }

    /**
     * Show verifikasi dashboard for panitia/pic.
     */
    public function index(Bimtek $bimtek): View
    {
        // Authorization
        $this->authorizePicPanitia($bimtek);
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        // Get all peserta with their documents
        $pesertaList = $bimtek->peserta()
            ->with(['dokumenPersyaratan' => function ($query) use ($bimtek) {
                $query->where('bimtek_id', $bimtek->id)
                    ->latest('uploaded_at');
            }])
            ->get()
            ->map(function ($peserta) use ($bimtek, $jenisDokumenWajib) {
                $assignment = DB::table('bimtek_user')
                    ->where('bimtek_id', $bimtek->id)
                    ->where('user_id', $peserta->id)
                    ->first();

                $dokumenByJenis = $peserta->dokumenPersyaratan
                    ->whereIn('jenis_dokumen', $jenisDokumenWajib)
                    ->groupBy('jenis_dokumen')
                    ->map(fn ($items) => $items->first());

                return [
                    'user' => $peserta,
                    'status_verifikasi' => $assignment->status_verifikasi ?? 'invited',
                    'dokumen' => $dokumenByJenis,
                ];
            });

        return view('verifikasi-dokumen.index', compact('bimtek', 'pesertaList', 'jenisDokumenWajib'));
    }

    /**
     * Approve dokumen.
     */
    public function approve(Request $request, DokumenPersyaratanPeserta $dokumen): RedirectResponse
    {
        $bimtek = $dokumen->bimtek;
        $this->authorizePicPanitia($bimtek);

        // Update dokumen status
        $dokumen->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->input('catatan_verifikasi') ?? $request->input('catatan'),
        ]);

        // Check if all required documents are approved
        $this->updatePesertaStatusVerifikasi($bimtek, $dokumen->user_id);

        return redirect()
            ->route('bimtek.verifikasi-dokumen.index', $bimtek)
            ->with('success', 'Dokumen disetujui.');
    }

    /**
     * Reject dokumen.
     */
    public function reject(Request $request, DokumenPersyaratanPeserta $dokumen): RedirectResponse
    {
        $bimtek = $dokumen->bimtek;
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'catatan_verifikasi' => 'required|string|max:1000',
        ], [
            'catatan_verifikasi.required' => 'Catatan penolakan wajib diisi.',
        ]);

        // Update dokumen status
        $dokumen->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        // Update peserta status to 'rejected'
        DB::table('bimtek_user')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $dokumen->user_id)
            ->update(['status_verifikasi' => 'rejected']);

        // Send email notification to peserta
        try {
            $peserta = User::find($dokumen->user_id);
            $uploadUrl = route('bimtek.verifikasi-dokumen.upload-form', $bimtek);
            Mail::to($peserta->email)->send(
                new DokumenVerifiedRejectedMail($bimtek, $peserta, 'rejected', $validated['catatan_verifikasi'], $uploadUrl)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send document rejected email: '.$e->getMessage());
            \App\Models\LogSistem::warning(
                "Gagal mengirim email dokumen ditolak ke peserta {$dokumen->user_id} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                Auth::id()
            );
        }

        return redirect()
            ->route('bimtek.verifikasi-dokumen.index', $bimtek)
            ->with('success', 'Dokumen ditolak. Peserta akan dinotifikasi untuk upload ulang.');
    }

    /**
     * Update status verifikasi peserta based on dokumen status.
     */
    protected function updatePesertaStatusVerifikasi(Bimtek $bimtek, string $userId): void
    {
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        $allApproved = true;
        foreach ($jenisDokumenWajib as $jenis) {
            $dokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
                ->where('user_id', $userId)
                ->where('jenis_dokumen', $jenis)
                ->latest('uploaded_at')
                ->first();

            if (! $dokumen || $dokumen->status !== 'approved') {
                $allApproved = false;
                break;
            }
        }

        if ($allApproved) {
            DB::table('bimtek_user')
                ->where('bimtek_id', $bimtek->id)
                ->where('user_id', $userId)
                ->update(['status_verifikasi' => 'verified']);

            // Send email notification to peserta (verified)
            try {
                $peserta = User::find($userId);
                Mail::to($peserta->email)->send(
                    new DokumenVerifiedRejectedMail($bimtek, $peserta, 'verified')
                );
            } catch (\Exception $e) {
                Log::error('Failed to send document verified email: '.$e->getMessage());
                \App\Models\LogSistem::warning(
                    "Gagal mengirim email dokumen terverifikasi ke peserta {$userId} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                    Auth::id()
                );
            }
        }
    }

    /**
     * Preview dokumen (inline view).
     */
    public function preview(DokumenPersyaratanPeserta $dokumen)
    {
        $bimtek = $dokumen->bimtek;
        $user = Auth::user();

        // Authorization: PIC, Panitia, or the peserta who uploaded
        $isPicPanitia = $bimtek->pic_user_id === $user->id ||
                        $bimtek->panitia()->where('users.id', $user->id)->exists();
        $isOwner = $dokumen->user_id === $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat dokumen ini.');
        }

        $filePath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = Storage::disk('public')->mimeType($dokumen->file_path);

        // Only allow preview for PDF files
        if ($mimeType !== 'application/pdf') {
            return redirect()->route('bimtek.verifikasi-dokumen.download', $dokumen)
                ->with('info', 'File ini hanya bisa diunduh, tidak bisa dipratinjau.');
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$dokumen->file_name.'"',
        ]);
    }

    /**
     * Download dokumen.
     */
    public function download(DokumenPersyaratanPeserta $dokumen)
    {
        $bimtek = $dokumen->bimtek;
        $user = Auth::user();

        // Authorization: PIC, Panitia, or the peserta who uploaded
        $isPicPanitia = $bimtek->pic_user_id === $user->id ||
                        $bimtek->panitia()->where('users.id', $user->id)->exists();
        $isOwner = $dokumen->user_id === $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }

    /**
     * Authorization helper: Only PIC or Panitia can verify.
     */
    protected function authorizePicPanitia(Bimtek $bimtek): void
    {
        $user = Auth::user();

        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('users.id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola verifikasi dokumen.');
        }
    }
}
