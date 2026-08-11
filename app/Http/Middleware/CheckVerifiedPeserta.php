<?php

namespace App\Http\Middleware;

use App\Models\Bimtek;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckVerifiedPeserta
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memblokir akses peserta yang belum verified dokumen
     * untuk fitur absensi, tugas, dan sertifikat jika Bimtek butuh verifikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil data bimtek dari parameter URL
        $bimtek = $request->route('bimtek');

        if (! $bimtek instanceof Bimtek) {
            return $next($request);
        }

        // 2. Jika bimtek bebas dokumen, langsung persilakan masuk
        if (! $bimtek->butuh_verifikasi_dokumen) {
            return $next($request);
        }

        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }

        // 3. Jika yang akses adalah Panitia / PIC / Admin, biarkan lewat (Bypass)
        $roleName = $user->role->nama_peran ?? '';
        if (!in_array($roleName, ['Peserta', 'Peserta Eksternal'])) {
            return $next($request);
        }

        // 4. Cek status peserta di tabel pivot terbaru (bimtek_pesertas)
        $pivot = DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $pivot) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar di kelas Bimtek ini.');
        }

        $statusVerifikasi = $pivot->status_verifikasi ?? 'invited';

        // 5. Logika Gembok Cerdas
        if ($statusVerifikasi === 'pending') {
            // Jika masih antre, kembalikan ke Dasbor
            return redirect()->route('dashboard')
                ->with('error', 'Akses dikunci! Anda harus menunggu panitia menyetujui berkas Anda sebelum bisa masuk ke kelas.');
        } elseif ($statusVerifikasi === 'rejected') {
            // Jika ditolak, paksa lari ke form perbaikan (upload ulang)
            return redirect()->route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
                ->with('error', 'Berkas Anda sebelumnya ditolak panitia. Silakan unggah perbaikan dokumen untuk masuk ke kelas.');
        } elseif ($statusVerifikasi !== 'verified') {
            // Pengaman darurat untuk status tidak dikenal
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Jika statusnya 'verified', gembok terbuka!
        return $next($request);
    }
}