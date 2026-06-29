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
     * Tampilkan halaman form unggah berkas persyaratan bagi peserta.
     */
    public function uploadForm(Bimtek $bimtek): View
    {
        $user = Auth::user();

        // REFAKTORISASI: Memeriksa keanggotaan menggunakan jembatan peserta baru
        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();
        if (! $isPeserta) {
            abort(403, 'Anda bukan peserta resmi dari kegiatan bimtek ini.');
        }

        // REFAKTORISASI: Mengalihkan pembacaan data dari bimtek_user ke bimtek_pesertas
        $assignment = DB::table('bimtek_pesertas')
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
     * Simpan file berkas yang diunggah oleh peserta ke dalam storage lokal.
     */
    public function upload(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $user = Auth::user();
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        $validated = $request->validate([
            'jenis_dokumen' => ['required', Rule::in($jenisDokumenWajib)],
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'file.max' => 'Ukuran file maksimal 2MB.',
            'file.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
        ]);

        // Cari dan hapus berkas fisik versi lama jika peserta melakukan unggah ulang (re-upload)
        $oldDokumen = DokumenPersyaratanPeserta::where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->where('jenis_dokumen', $validated['jenis_dokumen'])
            ->first();

        if ($oldDokumen) {
            Storage::disk('public')->delete($oldDokumen->file_path);
            $oldDokumen->delete();
        }

        $file = $request->file('file');
        $fileName = time().'_'.Str::slug($validated['jenis_dokumen']).'_'.$user->id.'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('dokumen_persyaratan', $fileName, 'public');

        DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        // REFAKTORISASI: Naikkan status kelulusan berkas di tabel bridge bimtek_pesertas menjadi 'pending'
        DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->whereIn('status_verifikasi', ['invited', 'rejected'])
            ->update(['status_verifikasi' => 'pending']);

        $jenisLabel = Str::of($validated['jenis_dokumen'])->replace('_', ' ')->title();

        return redirect()
            ->route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
            ->with('success', "Dokumen {$jenisLabel} berhasil diunggah. Menunggu proses pemeriksaan panitia.");
    }

    /**
     * Tampilkan dasbor meja verifikasi berkas bagi kelompok kerja panitia.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizePicPanitia($bimtek);
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        // Mengumpulkan daftar peserta beserta lampiran file dokumennya
        $pesertaList = $bimtek->peserta()
            ->with(['dokumenPersyaratan' => function ($query) use ($bimtek) {
                $query->where('bimtek_id', $bimtek->id)->latest('uploaded_at');
            }])
            ->get()
            ->map(function ($peserta) use ($bimtek, $jenisDokumenWajib) {
                // REFAKTORISASI: Membaca status checklist kelulusan dari tabel jembatan baru
                $assignment = DB::table('bimtek_pesertas')
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
     * Aksi Persetujuan Dokumen oleh Panitia.
     */
    public function approve(Request $request, DokumenPersyaratanPeserta $dokumen): RedirectResponse
    {
        $bimtek = $dokumen->bimtek;
        $this->authorizePicPanitia($bimtek);

        $dokumen->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->input('catatan_verifikasi') ?? $request->input('catatan'),
        ]);

        // Menjalankan pengecekan otomatis, jika semua berkas wajib sudah disetujui, luluskan peserta
        $this->updatePesertaStatusVerifikasi($bimtek, $dokumen->user_id);

        return redirect()
            ->route('bimtek.verifikasi-dokumen.index', $bimtek->id)
            ->with('success', 'Dokumen dinyatakan sah dan disetujui.');
    }

    /**
     * Aksi Penolakan Dokumen oleh Panitia (Disertai catatan koreksi).
     */
    public function reject(Request $request, DokumenPersyaratanPeserta $dokumen): RedirectResponse
    {
        $bimtek = $dokumen->bimtek;
        $this->authorizePicPanitia($bimtek);

        $validated = $request->validate([
            'catatan_verifikasi' => 'required|string|max:1000',
        ], [
            'catatan_verifikasi.required' => 'Alasan penolakan dokumen wajib diisi agar peserta tahu bagian yang salah.',
        ]);

        $dokumen->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        // REFAKTORISASI: Kunci status verifikasi peserta di tabel bridge menjadi 'rejected'
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

        // Jika seluruh syarat dokumen terpenuhi tanpa cela, ubah status final peserta menjadi verified
        if ($allApproved) {
            // REFAKTORISASI: Perbarui status akhir kelulusan langsung ke tabel jembatan bimtek_pesertas
            DB::table('bimtek_pesertas')
                ->where('bimtek_id', $bimtek->id)
                ->where('user_id', $userId)
                ->update(['status_verifikasi' => 'verified']);

            try {
                $peserta = User::find($userId);
                Mail::to($peserta->email)->send(new DokumenVerifiedRejectedMail($bimtek, $peserta, 'verified'));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email kelulusan dokumen.');
            }
        }
    }

    /**
     * Fitur Pratinjau Berkas Persyaratan (Khusus format berkas PDF).
     */
    public function preview(DokumenPersyaratanPeserta $dokumen)
    {
        $bimtek = $dokumen->bimtek;
        $user = Auth::user();

        $isPicPanitia = $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isOwner = $dokumen->user_id === $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Anda tidak memiliki hak otoritas untuk melihat berkas dokumen ini.');
        }

        $filePath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = Storage::disk('public')->mimeType($dokumen->file_path);

        if ($mimeType !== 'application/pdf') {
            return redirect()->route('bimtek.verifikasi-dokumen.download', $dokumen->id)
                ->with('info', 'Format berkas non-PDF hanya mendukung opsi unduh langsung.');
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$dokumen->file_name.'"',
        ]);
    }

    /**
     * Unduh berkas fisik dokumen persyaratan.
     */
    public function download(DokumenPersyaratanPeserta $dokumen)
    {
        $bimtek = $dokumen->bimtek;
        $user = Auth::user();

        $isPicPanitia = $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
        $isOwner = $dokumen->user_id === $user->id;

        if (! $isPicPanitia && ! $isOwner && ! $user->isAdminIt()) {
            abort(403, 'Akses ditolak.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }

    /**
     * Proteksi Keamanan Akses Meja Verifikasi Berkegiatan.
     */
    protected function authorizePicPanitia(Bimtek $bimtek): void
    {
        $user = Auth::user();
        $isPic = $bimtek->pic_user_id === $user->id;
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();

        if (! $isPic && ! $isPanitia) {
            abort(403, 'Wewenang terbatas! Modul ini dikunci khusus bagi PIC atau jajaran Panitia Pokja.');
        }
    }
}