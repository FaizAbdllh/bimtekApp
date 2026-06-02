<?php

namespace App\Http\Middleware;

use App\Models\Bimtek;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVerifiedPeserta
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memblokir akses peserta yang belum verified dokumen
     * untuk fitur absensi, tugas, dan sertifikat jika Bimtek butuh verifikasi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get bimtek from route parameter
        $bimtek = $request->route('bimtek');

        if (! $bimtek instanceof Bimtek) {
            return $next($request);
        }

        // Skip check if bimtek doesn't require verification
        if (! $bimtek->butuh_verifikasi_dokumen) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip check if user is not authenticated
        if (! $user) {
            return $next($request);
        }

        // Check if user is peserta in this bimtek
        $pivot = $bimtek->users()
            ->where('users.id', $user->id)
            ->where('bimtek_user.peran_kontekstual', 'peserta')
            ->first();

        // Skip check if user is not a peserta (maybe PIC or panitia)
        if (! $pivot) {
            return $next($request);
        }

        // Get verification status from pivot
        $statusVerifikasi = $pivot->pivot->status_verifikasi ?? 'invited';

        // Block access if not verified
        if ($statusVerifikasi !== 'verified') {
            return redirect()
                ->route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
                ->with('warning', 'Anda harus menyelesaikan verifikasi dokumen terlebih dahulu sebelum mengakses fitur ini.');
        }

        return $next($request);
    }
}
