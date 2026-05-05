<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBimtekRole
{
    /**
     * Handle an incoming request.
     * Cek apakah user memiliki peran kontekstual tertentu dalam bimtek
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$peranKontekstual  Daftar peran kontekstual yang diizinkan (pic, panitia, peserta)
     */
    public function handle(Request $request, Closure $next, string ...$peranKontekstual): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Ambil bimtek_id dari route parameter
        $bimtekId = $request->route('bimtek') ?? $request->route('bimtek_id');
        
        if (!$bimtekId) {
            abort(400, 'Bimtek ID tidak ditemukan.');
        }

        // Ambil ID jika objek Bimtek
        if (is_object($bimtekId)) {
            $bimtekId = $bimtekId->id;
        }

        $user = $request->user();
        $userPeran = $user->getPeranKontekstual($bimtekId);

        // Cek apakah user memiliki salah satu peran kontekstual yang diizinkan
        if (!$userPeran || !in_array($userPeran, $peranKontekstual)) {
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }

        return $next($request);
    }
}
