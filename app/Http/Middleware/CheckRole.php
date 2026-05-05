<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Cek apakah user memiliki salah satu role yang diizinkan
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Daftar role yang diizinkan (bisa lebih dari satu)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $request->user()->loadMissing('role');
        $userRole = trim((string) ($request->user()->role->nama_peran ?? ''));

        // Normalisasi parameter agar aman jika role dikirim sebagai CSV tunggal atau varargs.
        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            foreach (explode(',', (string) $roleParam) as $roleName) {
                $roleName = trim($roleName);
                if ($roleName !== '') {
                    $allowedRoles[] = $roleName;
                }
            }
        }

        $hasAccess = false;
        foreach ($allowedRoles as $allowedRole) {
            if (strcasecmp($userRole, $allowedRole) === 0) {
                $hasAccess = true;
                break;
            }
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
