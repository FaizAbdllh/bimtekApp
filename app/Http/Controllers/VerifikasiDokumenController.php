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
use Illuminate\View\View;

class VerifikasiDokumenController extends Controller
{
    /**
     * Tampilkan halaman form unggah berkas persyaratan bagi peserta (Untuk perbaikan berkas).
     */
    public function uploadForm(Bimtek $bimtek): View
    {
        $user = Auth::user();

        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();
        if (! $isPeserta) {
            abort(403, 'Anda bukan peserta resmi dari kegiatan bimtek ini.');
        }

        $assignment = DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->first();

        $syaratDokumens = $bimtek->syaratDokumens;

        // 💡 Murni menggunakan relasi syarat_dokumen_id
        $userDokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->latest('uploaded_at')
            ->get();

        $dokumenMap = $userDokumen->keyBy('syarat_dokumen_id');

        return view('verifikasi-dokumen.upload', compact(
            'bimtek',
            'assignment',
            'dokumenMap',
            'syaratDokumens'
        ));
    }

    /**
     * Simpan file berkas yang diunggah ulang oleh peserta (revisi/perbaikan).
     */
    public function upload(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'syarat_dokumen_id' => 'required|exists:syarat_dokumens,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'file.max' => 'Ukuran file maksimal 2MB.',
            'file.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
        ]);

        $syaratId = $validated['syarat_dokumen_id'];

        $oldDokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->where('syarat_dokumen_id', $syaratId)
            ->first();

        if ($oldDokumen) {
            Storage::disk('public')->delete($oldDokumen->file_path);
            // Kita tidak perlu delete datanya, cukup updateOrCreate nanti
        }

        $file = $request->file('file');
        $fileName = time().'_rev_'.$user->id.'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs("dokumen-persyaratan/{$bimtek->id}", $fileName, 'public');

        DokumenPersyaratanPeserta::updateOrCreate(
            [
                'bimtek_id' => $bimtek->id,
                'user_id' => $user->id,
                'syarat_dokumen_id' => $syaratId,
            ],
            [
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'status' => 'pending',
                'uploaded_at' => now(),
            ]
        );

        DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->whereIn('status_verifikasi', ['invited', 'rejected'])
            ->update(['status_verifikasi' => 'pending']);

        return redirect()
            ->route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
            ->with('success', "Dokumen revisi berhasil diunggah. Menunggu pemeriksaan panitia.");
    }

    /**
     * Tampilkan dasbor meja verifikasi berkas bagi kelompok kerja panitia.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizePicPanitia($bimtek);
        
        $syaratDokumens = $bimtek->syaratDokumens;

        $pesertaList = $bimtek->peserta()
            ->with(['dokumenPersyaratan' => function ($query) use ($bimtek) {
                $query->where('bimtek_id', $bimtek->id)->latest('uploaded_at');
            }])
            ->get()
            ->map(function ($peserta) use ($bimtek) {
                $assignment = DB::table('bimtek_pesertas')
                    ->where('bimtek_id', $bimtek->id)
                    ->where('user_id', $peserta->id)
                    ->first();

                // 💡 Pemetakan file berdasarkan syarat_dokumen_id
                $dokumenBySyaratId = $peserta->dokumenPersyaratan->keyBy('syarat_dokumen_id');

                return [
                    'user' => $peserta,
                    'status_verifikasi' => $assignment->status_verifikasi ?? 'invited',
                    'dokumen' => $dokumenBySyaratId,
                ];
            });

        return view('verifikasi-dokumen.index', compact('bimtek', 'pesertaList', 'syaratDokumens'));
    }

    /**
     * Aksi Persetujuan Dokumen oleh Panitia.
     */
    public function approve(Request $request, Bimtek $bimtek, string $userId, string $syaratId): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $dokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $userId)
            ->where('syarat_dokumen_id', $syaratId)
            ->firstOrFail();

        $dokumen->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->input('catatan_verifikasi') ?? $request->input('catatan'),
        ]);

        $this->updatePesertaStatusVerifikasi($bimtek, $dokumen->user_id);

        return redirect()
            ->route('bimtek.verifikasi-dokumen.index', $bimtek->id)
            ->with('success', 'Dokumen dinyatakan sah dan disetujui.');
    }

    /**
     * Aksi Penolakan Dokumen oleh Panitia (Disertai catatan koreksi).
     */
    public function reject(Request $request, Bimtek $bimtek, string $userId, string $syaratId): RedirectResponse
    {
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'catatan_verifikasi' => 'required|string|max:1000',
        ]);

        $dokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $userId)
            ->where('syarat_dokumen_id', $syaratId)
            ->firstOrFail();

        $dokumen->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $dokumen->user_id)
            ->update(['status_verifikasi' => 'rejected']);

        try {
            $peserta = User::find($dokumen->user_id);
            $uploadUrl = route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id);
            Mail::to($peserta->email)->send(
                new DokumenVerifiedRejectedMail($bimtek, $peserta, 'rejected', $validated['catatan_verifikasi'], $uploadUrl)
            );
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email notifikasi penolakan dokumen: '.$e->getMessage());
        }

        return redirect()
            ->route('bimtek.verifikasi-dokumen.index', $bimtek->id)
            ->with('success', 'Dokumen ditolak. Sistem telah mengirimkan email instruksi perbaikan kepada peserta.');
    }

    /**
     * Mesin Otomatisasi Kelulusan Verifikasi Berkas DIPA BBPMP Sumbar.
     */
    protected function updatePesertaStatusVerifikasi(Bimtek $bimtek, string $userId): void
    {
        $jumlahDokumenWajib = DB::table('syarat_dokumens')
            ->where('bimtek_id', $bimtek->id)
            ->where('is_wajib', 1)
            ->count();

        // 💡 PERBAIKAN: Menghapus typo fatal 'dokumen_persyaratan_peserta('
        $jumlahApproved = DB::table('dokumen_persyaratan_peserta')
            ->join('syarat_dokumens', 'dokumen_persyaratan_peserta.syarat_dokumen_id', '=', 'syarat_dokumens.id')
            ->where('dokumen_persyaratan_peserta.bimtek_id', $bimtek->id)
            ->where('dokumen_persyaratan_peserta.user_id', $userId)
            ->where('syarat_dokumens.is_wajib', 1)
            ->where('dokumen_persyaratan_peserta.status', 'approved')
            ->count();

        if ($jumlahDokumenWajib > 0 && $jumlahDokumenWajib === $jumlahApproved) {
            DB::table('bimtek_pesertas')
                ->where('bimtek_id', $bimtek->id)
                ->where('user_id', $userId)
                ->update(['status_verifikasi' => 'verified', 'updated_at' => now()]);

            try {
                $peserta = User::find($userId);

                if ($peserta->is_active == 0) {
                    $magicToken = Str::random(64);
                    $peserta->update([
                        'token_hash' => $magicToken,
                        'expires_at' => now()->addDays(7),
                    ]);

                    $activationUrl = url('/aktivasi/' . $magicToken);

                    Mail::to($peserta->email)->send(
                        new DokumenVerifiedRejectedMail($bimtek, $peserta, 'verified', null, $activationUrl)
                    );
                } else {
                    Mail::to($peserta->email)->send(
                        new DokumenVerifiedRejectedMail($bimtek, $peserta, 'verified')
                    );
                }
            } catch (\Exception $e) {
                Log::error('Gagal memproses otomatisasi rilis token/email kelulusan: '.$e->getMessage());
            }
        }
    }

    /**
     * Unduh berkas fisik dokumen persyaratan.
     */
    public function download(Bimtek $bimtek, string $userId, string $syaratId)
    {
        $user = Auth::user();
        $isPicPanitia = $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isOwner = $userId === (string) $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        $dokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $userId)
            ->where('syarat_dokumen_id', $syaratId)
            ->firstOrFail();

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }

    protected function authorizePicPanitia(Bimtek $bimtek): void
    {
        $user = Auth::user();
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Wewenang terbatas! Modul ini dikunci khusus bagi PIC atau jajaran Panitia Pokja.');
        }
    }
    /**
     * Fitur Pratinjau Berkas Persyaratan (Khusus format berkas PDF).
     */
    public function preview(Bimtek $bimtek, string $userId, string $syaratId)
    {
        $user = Auth::user();
        $isPicPanitia = $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isOwner = $userId === (string) $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki hak otoritas untuk melihat berkas dokumen ini.');
        }

        $dokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $userId)
            ->where('syarat_dokumen_id', $syaratId)
            ->firstOrFail();

        $filePath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = Storage::disk('public')->mimeType($dokumen->file_path);

        if ($mimeType !== 'application/pdf') {
            return redirect()->route('bimtek.verifikasi-dokumen.download', [
                'bimtek' => $bimtek->id, 'userId' => $userId, 'syaratId' => $syaratId
            ])->with('info', 'Format berkas non-PDF hanya mendukung opsi unduh langsung.');
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$dokumen->file_name.'"',
        ]);
    }
}